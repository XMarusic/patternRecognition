<?php
	ini_set('max_execution_time', 300);
	ini_set('memory_limit', '-1');
	require_once('getid3/getid3.php');
	define('ROOT', $_SERVER['DOCUMENT_ROOT']);
	include_once ROOT.'/url.php';

	$fileFormats = ['mp3', 'wav', 'flac'];
	$recurseFile = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
		RecursiveIteratorIterator::SELF_FIRST
	);
	
	//$dictionary = array();
	$dictionary = json_decode(file_get_contents($songList), true);
	$listed_songs = json_decode(file_get_contents($song_names), true);

	$srno = sizeof($dictionary);
	$Lastsrno = $srno;

	foreach($recurseFile as $pathname => $file){
		$filename = $file->getFileName();
		$rawfilename = pathinfo($filename, PATHINFO_FILENAME);
		// var_dump($listed_songs);
		// echo "<br>";
		// if(!in_array($rawfilename, $listed_songs)) 
		// 	echo $rawfilename."<br>";
		if(!in_array($rawfilename, $listed_songs) && in_array(pathinfo($filename, PATHINFO_EXTENSION), $fileFormats)){
			
			array_push($listed_songs, $rawfilename);

			$getID3 = new getID3;
			$analyze = $getID3->analyze($file);

			$Image = "";
			$displayName = "";
			$albumName = "";
			$artistName = "";
			$genreName = "";

			$id3v = ['id3v1', 'id3v2', 'id3v3', 'id3v4'];

			// if($srno == 0)
			// 	var_dump($analyze);

			foreach ($id3v as $id3) {
				if(isset($analyze['tags']) 
					&& isset($analyze['tags'][$id3]) 
					&& isset($analyze['tags'][$id3])){

					$tagPresent = $analyze['tags'][$id3];
					if(isset($tagPresent['title']))
						$displayName = $tagPresent['title'][0];
					if(isset($tagPresent['album']))
						$albumName = $tagPresent['album'][0];
					if(isset($tagPresent['artist']))
						$artistName = $tagPresent['artist'][0];
					if(isset($tagPresent['genre']))
						$genreName = $tagPresent['genre'][0];

					break;
				}
			}

			if($displayName == "") 
				$displayName = $rawfilename;
			
			if(isset($analyze['comments']) 
				&& isset($analyze['comments']['picture'])
				&& isset($analyze['comments']['picture'][0])){
				$albumArt = $analyze['comments']['picture'][0];
				$Image='data:'.$analyze['comments']['picture'][0]['image_mime'].';charset=utf-8;base64,'.base64_encode($analyze['comments']['picture'][0]['data']);
            } 
            if($Image === "")
            	$Image = $defaultImage;

			$dictionary[] = array('displayName'=>$displayName, 'key' => $srno, 'albumName'=>$albumName, 'artist'=>$artistName, 'genre' => $genreName, 'path'=>$pathname, 'albumArt'=>$Image);
			$srno = $srno + 1;
		}
	}

	usort($dictionary, function($a, $b) {
	    if ($a==$b) return 0;
   		return ($a<$b)?-1:1;
	});
	
	$indexMap = array();
	for($i = 0; $i < $srno; $i++){
		array_push($indexMap, 0);
	}
	for ($i=0; $i < $srno; $i++) {
		$key = intval($dictionary[$i]['key']);
		$indexMap[$key] = $i;
	}

	if(file_put_contents($songList, json_encode($dictionary)) 
		&& file_put_contents($song_names, json_encode($listed_songs))
		&& file_put_contents($index_map_path, json_encode($indexMap)))
		echo sizeof($dictionary)." ".$srno." ".sizeof($indexMap)."<br>";
	//var_dump($indexMap);

?>