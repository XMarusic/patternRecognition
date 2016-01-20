<?php 

	function loadFromFile(){
		return json_decode(file_get_contents('songListtemp.json'), true);

	}

	function loadDurationFile(){
		return json_decode(file_get_contents('inc/duration.json'), true);
	}

	function loadHistoryFile(){
		return json_decode(file_get_contents('inc/history.json'), true);
	}

	function getFiles(){
		$dictionary = loadFromFile();
		$durationArray = loadDurationFile();
		foreach ($dictionary as $key => $value) {
			$numOfSong = $key + 1;
			$displayName = $value['displayName'];
			$Image = $value['albumArt'];

			$array_key = array_search($key, array_column($durationArray, 'key'));
			$expected_duration = round($durationArray[$array_key]['expected_duration'], 3);
			$duration = round(($expected_duration * $durationArray[$array_key]['duration'])/60, 2); 
			?>

			<li class="songListLi">
                <div class="inline mid-align songListNumber"><?php echo $numOfSong.".";?></div>
                <div class="inline mid-align listAlbumArt">
                    <img class="songListAlbumArt" src="<?php echo @$Image;?>">
                </div>
                <div class="inline mid-align playIconDiv">
                    <img name="play_<?php echo $key;?>" class="playIconEntireList listSongPlayIcon" src="images/play.png">
                </div>
                <div class="inline mid-align songNameList"><?php echo $displayName;?></div>
                <div class="inline mid-align expectedPlayTime"><?php echo $duration; ?> / <?php echo $expected_duration;?></div>
            </li>

			<?php
		}
	}

	function getTopPicks(){
		$dictionary = loadFromFile();
		$topArray = loadDurationFile();
		usort($topArray, function($a, $b)
		{
		    if ($a['expected_duration']==$b['expected_duration']) return 0;
   			return ($a['expected_duration']>$b['expected_duration'])?-1:1;
		});

		$maxCount = 8;
		for($i = 0; $i<$maxCount; $i++){
			$key = $topArray[$i]['key'];

			$displayName = $dictionary[$key]['displayName'];
			$Image = $dictionary[$key]['albumArt'];

			?>
			<li class="inline mid-align broadListLi">
                <div class="inline mid-align broadAlbumArt">
                    <img class="broadListAlbumArt" src="<?php echo @$Image;?>">
                    <div class="playOnAlbumArt">
                        <div class="playAawrapper">
                            <img class="albumArtPlay listSongPlayIcon" name="play_<?php echo $key;?>"  src="images/play.png">
                        </div>
                    </div>
                </div>
                <div class="inline mid-align broadSongNameList"><?php echo $displayName;?></div>
            </li>
			<?php
		}
	}

	function getHistory(){
		$historyarray = loadHistoryFile();
		$dictionary = loadFromFile();
		for($i = 0; $i<15 && $i < sizeof($historyarray); $i++){
			$key = $historyarray[$i];
		?>
			<li class="historyUlLi leftBarUlLi" hisref="play_<?php echo $key;?>"><?php echo $dictionary[$key]['displayName']; ?></li>
		<?php
		}
	}
?>