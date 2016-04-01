var songStack = [];
var songWindow = [];
var decay = 0.001;
var windowLength = 10;

//var songPath = "data/json/songList.json";
var songPath = "data/json/songListtemp.json";

var sim_matrix_path = "data/json/sim_matrix.json";
var index_map_path = "data/json/index_map.json";
var duration_path = "data/json/duration.json";

function filterRow(array, findkey){
	return array.filter(function (song) { return song.key == findkey });
}

function addDecay(){
	$.ajax({
		url: '/inc/log.php',
		data: {addDecay:'true'},
		success: function(data){
			//
		}
	})
	$('.expectedPlayTime').each(function(){
		var sed = $(this).find('.song-eduration');
		var med = $(this).find('.song-mduration');
		var et = sed.html();
		et = +(et);
		var mt = med.html();
		mt = +(mt);
		var tot = mt/et;
		if(et >= decay){
			et = et - decay;
			et = et.toFixed(3);
			mt = et * tot;
			mt = mt.toFixed(2);
		}
		med.html(mt);
		sed.html(et);
	})
}

function detectEnd(array, index_map, sim_matrix, duration_array){
	var audio = document.getElementsByTagName('audio')[0];
	audio.onended = function(){
		updateSongTime(audio, sim_matrix, duration_array);
		resetSongList();
		var reference = $('audio source').attr('ref').split('_').pop();
		reference = "play_"+reference;

		playnextSong(array, reference, index_map, sim_matrix, duration_array);
	}
}

function detectPlayerActivity(){
	$('audio').on('pause', function(){
		var name = $(this).find('source').attr('ref');
		var key = name.split('_').pop();
		$('.listSongPlayIcon[name="'+name+'"]').attr({'src':'images/play.png', 'name':'playpause_'+key});
		$(this).find('source').attr('ref', 'playpause_'+key);
	})
	$('audio').on('play', function(){
		var name = $(this).find('source').attr('ref');
		var key = name.split('_').pop();
		$('.listSongPlayIcon[name="'+name+'"]').attr({'src':'images/pause.png', 'name':'pause_'+key});
		$(this).find('source').attr('ref', 'pause_'+key);
		updateAppearance(key);
	})
}

function findMatch(attr, query){
	var find_match_in = ['displayName', 'albumName', 'artist', 'genre'];
	query = query.toLowerCase();
	//console.log(attr);
	for(var value in find_match_in){
	//	console.log(find_match_in[value] + " " + attr[find_match_in[value]] + " " + query);
		if(attr[find_match_in[value]].toLowerCase().indexOf(query) >= 0){
	//		console.log("matched");
			return true;
		}
	}
	return false;
}

function insertInHistory(array, key, index_map){
	var rowkey = index_map[key];
	
	$('.historyUL').prepend('<li class="historyUlLi leftBarUlLi" hisref="play_'+key+'">'+ array[rowkey].displayName+'</li>');
		
	$.ajax({
		url: "/inc/log.php",
		data: {key: key,},
		type: 'GET',
		success: function(data){
			//
		}
	})
}

function logQuery(thiskey){
	var contextCount = 1;
	var maxContext = 2;
	var context = [];
	while(contextCount <= maxContext){
		var obj = $('.historyUlLi:nth-child('+contextCount+')').attr('hisref');
		if(obj == undefined)
			break;
		var key = obj.split('_').pop();
		context.push(key);
		contextCount++;
	}
	if(contextCount < maxContext){
		while(contextCount <= maxContext){
			context.push(null);
			contextCount++;
		}
	}
	var key = thiskey;
}

function playHistorySong(array, index_map, sim_matrix, duration_array){
	$(document).on('click','.leftBarUlLi', function(){
		var thisEleName = $(this).attr('hisref');
		var thisEle = $('.listSongPlayIcon[name="'+thisEleName+'"]');
		if(thisEle.length) {
			triggerTrackSelection(thisEle, array, index_map, sim_matrix, duration_array);
		}
	})	
}

function playnextSong(array, reference, index_map, sim_matrix, duration_array){
	var ulreference = $('audio source').attr('ul-ref');

	var nextele = $('#'+ulreference).find('.listSongPlayIcon[name="'+reference+'"]');
	nextele = nextele.parents('li').next().find('.listSongPlayIcon');

	triggerTrackSelection(nextele, array, index_map, sim_matrix, duration_array);
}

function playPauseSelected(array, index_map, sim_matrix, duration_array){
	$(document).on('click', '.listSongPlayIcon', function(){
		var thisEle = $(this);
		triggerTrackSelection(thisEle, array, index_map, sim_matrix, duration_array);
	})
}

function resetSongList(){
	$('.listSongPlayIcon').each(function(){
		$(this).attr({'src':'images/play.png', 'name':'play_'+($(this).attr('name').split('_')[1])});
	})
	$('.playOnAlbumArt').each(function(){
		$(this).css('opacity', '');
	})
	$('.leftBarUlLi').each(function(){
		$(this).css('color', '');
	})
}

function searchSongs(array, index_map){
	$('.searchInput').on('keydown', function(e){
		if((e.keyCode || e.which) == 13) {

			$('.resultSongListUl').html('');
			var query = $(this).val();
			var search = [];
			//console.log(index_map);
			for(var key in array){
				var rowkey = index_map[key];
				// console.log(key);
				// console.log(rowkey);
				// console.log(array[rowkey]);
				
				if( findMatch(array[rowkey], query)){
					search.push({'displayName': array[rowkey].displayName, 'albumArt': array[rowkey].albumArt, 'key':key});
				}
			}
			if(search.length){
				showResult(search, query);
			} else showNoResult(query);
		}
	})
}

function showNoResult(query){
	$('.broadHeader').html("We couldn't get any result for '"+query+"'");
}

function showResult(search, query){
	$('.broadHeader').html("Search result for '"+query+"'");
	for(var key in search){
		var putResult = '<li class="inline mid-align broadListLi">';
	    putResult += '<div class="inline mid-align broadAlbumArt">';
	    putResult += '<img class="broadListAlbumArt" src="'+search[key].albumArt+'">';
	    putResult += '<div class="playOnAlbumArt">';
	    putResult += '<div class="playAawrapper">';
	    putResult += '<img class="albumArtPlay listSongPlayIcon" name="play_'+search[key].key+'" src="images/play.png">';
	    putResult += '</div>';
	    putResult += '</div>';
	    putResult += '</div>';
	    putResult += '<div class="inline mid-align broadSongNameList">'+search[key].displayName+'</div>';
	    putResult += '</li>';

	  	$('.resultSongListUl').append(putResult);
	}
}

function triggerPrevNextSelection(array, index_map, sim_matrix, duration_array){
	
	$('.playNextMain').click(function(){
		var audio = document.getElementsByTagName('audio')[0];
		updateSongTime(audio, sim_matrix, duration_array);

		var reference = $('audio source').attr('ref');	

		playnextSong(array, reference, index_map, sim_matrix, duration_array);
	})
	$('.playPrev').click(function(){
		var ulreference = $('audio source').attr('ul-ref');

		if(songStack.length > 1){
			var audio = document.getElementsByTagName('audio')[0];
			updateSongTime(audio, sim_matrix, duration_array);

			var key = songStack.pop();
			key = songStack.pop();

			var newele = $('#'+ulreference).find('.listSongPlayIcon[name="play_'+key+'"]');

			triggerTrackSelection(newele, array, index_map);
		}
	})

}

function triggerTrackSelection(thisEle, array, index_map, sim_matrix, duration_array){
	var filename = thisEle.attr('name').split('_');
	var key = filename.pop();
		
	if(filename[0] == "play"){
		songStack.push(key);
		
		var audioref = $('audio source').attr('ref');
		if(audioref !== undefined){
			var audio = document.getElementsByTagName('audio')[0];
			updateSongTime(audio, sim_matrix, duration_array);
		}
		
		var path = array[index_map[key]].path;

		insertInHistory(array, key, index_map);
		resetSongList();
		updateAppearance(key);
		addDecay();

		var ulparentname = thisEle.parents('ul').attr('name');
		$('audio source').attr({'src':path, 'ref':'play_'+key, 'ul-ref':ulparentname});
		$('audio')[0].load();
		$('audio')[0].play();

	} else if(filename[0] == "playpause"){
		thisEle.attr({'src':'images/pause.png', 'name':'pause_'+key});
		$('audio')[0].play();
	} else {
		$('audio')[0].pause();
		thisEle.attr({'src':'images/play.png', 'name':'playpause_'+key});
	}
}

function updateAppearance(key){
	$('.songListLi').css('background', '');
	$('.songListLi[mainkey="'+key+'"]').css('background', '#212121');
	$('.leftBarUlLi[hisref="play_'+key+'"]').css('color','#f1f1f1');
	$('.listSongPlayIcon[name="pause_'+key+'"]').parents('.playOnAlbumArt').css('opacity', 1);
}

function updateSongTime(audio, sim_matrix, duration_array){
	var currentTime = audio.currentTime;
	var duration = audio.duration;

	var key = $('audio source').attr('ref').split('_').pop();

	var song_info_array = [];
	song_info_array.push(key, currentTime, duration);
	pushToSongWindow(song_info_array, sim_matrix, duration_array);
	
	if(isNaN(duration))
		duration = 0;
	if(isNaN(currentTime))
		currentTime = 0;

	$.ajax({
		url: "/inc/log.php",
		method: "GET",
		data: {durkey: key, duration: duration, currentTime: currentTime},
		success: function(new_expected_duration){
			new_expected_duration = (+(new_expected_duration)).toFixed(3);

			var duration_show_parent = $('li[mainkey='+key+']').find('.expectedPlayTime');
			duration_show_parent.find('.song-eduration').html(new_expected_duration);

			var new_mean_duration = ((new_expected_duration * duration)/60).toFixed(3);
			duration_show_parent.find('.song-mduration').html(new_mean_duration);
		}
	})
	
}


/** Similarity Matrix Stuffs **/

function pushToSongWindow(song_info_array, sim_matrix, duration_array){
	var songkey = song_info_array[0];
	var currentTime = song_info_array[1];
	var duration = song_info_array[2];

	if((currentTime/duration > 0.1) || (currentTime > 5)){
		for(var windowkey in songWindow){
			var prevSim = sim_matrix[songkey][windowkey];

			var ns_mean_time = duration_array[songkey];
			var ls_mean_time = duration_array[windowkey];

			//var weight = ns_mean_time * ls_mean_time;
			var weight = 1;
			var newSim = ((weight * (1/(windowkey+1)) * 2) + (prevSim * 3))/5;
			sim_matrix[songkey][windowkey] = sim_matrix[windowkey][songkey] = newSim;
			$.ajax({
				url: "/inc/log.php",
				data: {sim_matrix: sim_matrix},
				success: function(data){
					//			
				}
			})
		}
		if(songWindow.length > windowLength)
			var temp = song.pop();
		songWindow.unshift(songkey);
	}	
}


/** Similarity Matrix Stuffs End **/

$(document).ready(function(){
	$.getJSON(songPath, function(array){
		$.getJSON(index_map_path, function(index_map){
			$.getJSON(duration_path, function(duration_array){
				$.getJSON(sim_matrix_path, function(sim_matrix){
					console.log("start");
					playPauseSelected(array, index_map, sim_matrix, duration_array);
					detectPlayerActivity();

					searchSongs(array, index_map);
					playHistorySong(array, index_map, sim_matrix, duration_array);

					detectEnd(array, index_map, sim_matrix, duration_array);

					triggerPrevNextSelection(array, index_map, sim_matrix, duration_array);
				})
			})
		})
	})
});