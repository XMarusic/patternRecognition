<?php

	//$client = new MongoClient();

	//$db = $client->contexLog;

	if(isset($_GET['key'])){
		$historyarray = json_decode(file_get_contents("history.json"), true);
		
		$key = $_GET['key'];

		array_unshift($historyarray, $key);

		file_put_contents("history.json", json_encode($historyarray));
	}

	if(isset($_GET['duration'])){
		$durationarray = json_decode(file_get_contents("duration.json"), true);

		$key = $_GET['durkey'];
		$duration = $_GET['duration'];
		$currentTime = $_GET['currentTime'];

		$array_key = array_search($key, array_column($durationarray, 'key'));

		$prevDuration = $durationarray[$array_key]['expected_duration']; 
		$updatedDuration = ( $prevDuration + $currentTime ) / $duration;

		$durationarray[$array_key]['expected_duration'] = $updatedDuration;
		$durationarray[$array_key]['duration'] = $duration;

		file_put_contents("duration.json", json_encode($durationarray));
	}
?>