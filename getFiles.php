<?php
	ini_set('max_execution_time', 300);
	ini_set('memory_limit', '-1');
	require_once('getid3/getid3.php');
	$path = "tempSong/";
	$fileFormats = ['mp3', 'wav', 'flac'];
	$recurseFile = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
		RecursiveIteratorIterator::SELF_FIRST
	);

	$dictionary = array();
	foreach($recurseFile as $pathname => $file){
		$filename = $file->getFileName();
		if(in_array(pathinfo($filename, PATHINFO_EXTENSION), $fileFormats)){
			$getID3 = new getID3;
			$analyze = $getID3->analyze($file);
			
			if(isset($analyze['tags']) 
				&& isset($analyze['tags']['id3v1']) 
				&& isset($analyze['tags']['id3v1']['title']) 
				&& isset($analyze['tags']['id3v1']['title'][0]))
				$displayName = $analyze['tags']['id3v1']['title'][0];
			else $displayName = pathinfo($filename, PATHINFO_BASENAME);

			if(isset($analyze['comments']) 
				&& isset($analyze['comments']['picture'])
				&& isset($analyze['comments']['picture'][0])){
				$albumArt = $analyze['comments']['picture'][0];
				$Image='data:'.$analyze['comments']['picture'][0]['image_mime'].';charset=utf-8;base64,'.base64_encode($analyze['comments']['picture'][0]['data']);
            } else $albumArt = NULL;

			$dictionary[] = array('displayName'=>$displayName, 'path'=>$pathname, 'albumArt'=>$Image);
		}
	}

	usort($dictionary, function($a, $b) {
	    if ($a==$b) return 0;
   		return ($a<$b)?-1:1;
	});

	if(file_put_contents("songListtemp.json", json_encode($dictionary)))
		echo "Success";

?>