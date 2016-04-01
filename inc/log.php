<?php
	define('ROOT', $_SERVER['DOCUMENT_ROOT']);
	include ROOT.'/url.php';
	//$client = new MongoClient();

	//$db = $client->contexLog;

	if(isset($_GET['key'])){
		$historyarray = json_decode(file_get_contents('../'.$history), true);
		
		$key = $_GET['key'];

		array_unshift($historyarray, $key);

		file_put_contents('../'.$history, json_encode($historyarray));
	}

	if(isset($_GET['duration'])){
		$durationarray = json_decode(file_get_contents('../'.$duration), true);

		$key = $_GET['durkey'];
		$totalduration = $_GET['duration'];
		$currentTime = $_GET['currentTime'];

		$array_key = array_search($key, array_column($durationarray, 'key'));

		$old_expected_duration = $durationarray[$array_key]['expected_duration']; 
		$new_expected_duration = ( (3 * ($old_expected_duration * $totalduration)/4) + ($currentTime/4) ) / $totalduration;
		$new_expected_duration = round($new_expected_duration, 3);

		$durationarray[$array_key]['expected_duration'] = $new_expected_duration;
		$durationarray[$array_key]['duration'] = $totalduration;

		file_put_contents('../'.$duration, json_encode($durationarray));

		echo $new_expected_duration;
	}

	if(isset($_GET['addDecay'])){
		$durationarray = json_decode(file_get_contents('../'.$duration), true);
		$decay = 0.001;

		foreach ($durationarray as $key => $value) {
			if($value['expected_duration'] >= $decay){
				$durationarray[$key]['expected_duration'] = $value['expected_duration'] - $decay;
			}
		}

		file_put_contents('../'.$duration, json_encode($durationarray));
	}

	if(isset($_GET['sim_matrix'])){
		$sim_matrix_array = $_GET['sim_matrix'];
		file_put_contents('../'.$sim_matrix, json_encode($sim_matrix_array));
	}
?>