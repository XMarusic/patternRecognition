<?php 
	if(!defined('ROOT'))
		define('ROOT', $_SERVER['DOCUMENT_ROOT']);
	ini_set('memory_limit', '-1');
		
	function loadJsonFromFile($addr){
		return json_decode(file_get_contents($addr), true);
	}

	function loadFromFile(){
		include ROOT.'/url.php';
		return loadJsonFromFile($songList);
	}

	function loadDurationFile(){
		include ROOT.'/url.php';
		return loadJsonFromFile($duration);
	}

	function loadHistoryFile(){
		include ROOT.'/url.php';
		return loadJsonFromFile($history);
	}

	function loadIndexMapFile(){
		include ROOT.'/url.php';
		return loadJsonFromFile($index_map_path);
	}

	function getFiles(){
		$dictionary = loadFromFile();
		$durationArray = loadDurationFile();
		foreach ($dictionary as $key => $value) {
			$numOfSong = $key + 1;
			$displayName = $value['displayName'];
			$Image = $value['albumArt'];

			$songkey = $value['key'];

			$array_key = array_search($songkey, array_column($durationArray, 'key'));

			$expected_duration = round($durationArray[$array_key]['expected_duration'], 3);
			$meanduration = round(($expected_duration * $durationArray[$array_key]['duration'])/60, 2); 

			?>

			<li class="songListLi" mainkey="<?php echo $songkey;?>">
                <div class="inline mid-align songListNumber"><?php echo $numOfSong.".";?></div>
                <div class="inline mid-align listAlbumArt">
                    <img class="songListAlbumArt" <?php if($Image != "") { ?> src="<?php echo @$Image;?>"<?php } else { ?> src="images/default.png" <?php } ?>>
                </div>
                <div class="inline mid-align playIconDiv">
                    <img name="play_<?php echo $songkey;?>" class="playIconEntireList listSongPlayIcon" src="images/play.png">
                </div>
                <div class="inline mid-align songNameList"><?php echo $displayName;?></div>
                <div class="inline mid-align expectedPlayTime"><span class="song-mduration"><?php echo $meanduration; ?></span> / <span class="song-eduration"><?php echo $expected_duration;?></span></div>
            </li>

			<?php
		}
	}

	function getTopPicks(){

		$dictionary = loadFromFile();
		$topArray = loadDurationFile();
		$index_map = loadIndexMapFile();

		usort($topArray, function($a, $b)
		{
		    if ($a['expected_duration']==$b['expected_duration']) return 0;
   			return ($a['expected_duration']>$b['expected_duration'])?-1:1;
		});

		$maxCount = 8;
		for($i = 0; $i<$maxCount; $i++){
			$key = $topArray[$i]['key'];

			$songkey = $index_map[$key];
			//echo "KEY: ".$songkey;
			
			$displayName = $dictionary[$songkey]['displayName'];
			$Image = $dictionary[$songkey]['albumArt'];

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
		$index_map = loadIndexMapFile();

		for($i = 0; $i<15 && $i < sizeof($historyarray); $i++){
			$key = $historyarray[$i];
			$songkey = $index_map[$key];
		?>
			<li class="historyUlLi leftBarUlLi" hisref="play_<?php echo $key;?>"><?php echo $dictionary[$songkey]['displayName']; ?></li>
		<?php
		}
	}
?>