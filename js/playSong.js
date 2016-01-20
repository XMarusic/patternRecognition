var songStack = [];

function playPauseSelected(array){
	$(document).on('click', '.listSongPlayIcon', function(){
		var thisEle = $(this);
		triggerTrackSelection(thisEle, array);
	})
}

function triggerTrackSelection(thisEle, array){
	var filename = thisEle.attr('name').split('_');
	var key = filename.pop();
		
	if(filename[0] == "play"){
		songStack.push(key);

		var path = array[key].path;

		insertInHistory(array, key);
		resetSongList();
		updateAppearance(key);

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
	$('.leftBarUlLi[hisref="play_'+key+'"]').css('color','#f1f1f1');
	$('.listSongPlayIcon[name="play_'+key+'"]').attr({'src':'images/pause.png', 'name':'pause_'+key}).parents('.songListLi').css('background', '#212121');
	$('.listSongPlayIcon[name="pause_'+key+'"]').parents('.playOnAlbumArt').css('opacity', 1);
		
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

function loadArray(){
	var array = [];
	
	$.getJSON('/songListtemp.json', function (json) {
	for (var key in json) {
	    if (json.hasOwnProperty(key)) {
	    	var item = json[key];
	        array.push({'displayName':item.displayName, 'path':item.path, 'albumArt':item.albumArt});
	    }
	}
	});
	return array;
}

function searchSongs(array){
	$('.searchInput').on('keydown', function(e){
		if((e.keyCode || e.which) == 13) {
			$('.resultSongListUl').html('');
			var query = $(this).val();
			var search = [];
			for(var key in array){
				if(array[key].displayName.toLowerCase().indexOf(query.toLowerCase()) >= 0){
					search.push({'displayName':array[key].displayName, 'albumArt':array[key].albumArt, 'key':key});
				}
			}
			if(search.length){
				showResult(search, query);
			} else showNoResult(query);
		}
	})
}

function playHistorySong(array){
	$(document).on('click','.leftBarUlLi', function(){
		var thisEleName = $(this).attr('hisref');
		var thisEle = $('.listSongPlayIcon[name="'+thisEleName+'"]');
		if(thisEle.length) {
			triggerTrackSelection(thisEle, array);
		}
	})	
}

function insertInHistory(array, key){
	$('.historyUL').prepend('<li class="historyUlLi leftBarUlLi" hisref="play_'+key+'">'+array[key].displayName+'</li>');
		
	$.ajax({
		url: "/inc/log.php",
		data: {key: key,},
		type: 'GET',
		success: function(data){
			//
		}
	})
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

function showNoResult(query){
	$('.broadHeader').html("We couldn't get any result for '"+query+"'");
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

function updateSongTime(audio){
	var currentTime = audio.currentTime;
	var duration = audio.duration;

	var key = $('audio source').attr('ref').split('_').pop();
	
	if(isNaN(duration))
		duration = 0;
	if(isNaN(currentTime))
		currentTime = 0;

	$.ajax({
		url: "/inc/log.php",
		method: "GET",
		data: {durkey: key, duration: duration, currentTime: currentTime},
		success: function(){
			//
		}
	})
	
}

function detectEnd(){
	var audio = document.getElementsByTagName('audio')[0];
	audio.onended = function(){
		updateSongTime(audio);
		resetSongList();
		var reference = $('audio source').attr('ref').split('_').pop();
		reference = "play_"+reference;

		playnextSong(array, reference);
	}
}

function triggerPrevNextSelection(array){
	
	$('.playNextMain').click(function(){
		var reference = $('audio source').attr('ref');	
		playnextSong(array, reference);
	})
	$('.playPrev').click(function(){
		var ulreference = $('audio source').attr('ul-ref');
		var key = songStack.pop();
		key = songStack.pop();
		var newele = $('.contentSongList[name="'+ulreference+'"]').find('.listSongPlayIcon[name="play_'+key+'"]');
		triggerTrackSelection(newele, array);
	})

}

function playnextSong(array, reference){
	var ulreference = $('audio source').attr('ul-ref');
				
	var nextele = $('.contentSongList[name="'+ulreference+'"]').find('.listSongPlayIcon[name="'+reference+'"]').parents('li').next().find('.listSongPlayIcon');
	triggerTrackSelection(nextele, array);
}

$(document).ready(function(){
	array = loadArray();
	playPauseSelected(array);
	detectPlayerActivity();

	searchSongs(array);
	playHistorySong(array);

	detectEnd();

	triggerPrevNextSelection(array);
});