<?php
	include_once 'inc/loadFiles.php';
	include_once ROOT.'/url.php';

	$dictionary = loadFromFile();
	$size = sizeof($dictionary);

	$sim_arr = json_decode(file_get_contents($sim_matrix), true);

	for ($i=0; $i < $size; $i++) { 
		$t_sim_arr = array();
		if($i >= $srno)
			$sindex = 0;
		else {
			$sindex = $srno;
			$t_sim_arr = $sim_arr[$i];
		}

		for ($j=$sindex; $j < $size; $j++) { 
			array_push($t_sim_arr, "0");
		}
		if($i > $srno)
			$sim_arr[] = $t_sim_arr;
		else $sim_arr[$i] = $t_sim_arr; 
	}

	if(file_put_contents($sim_matrix, json_encode($sim_arr)))
		echo "Success<br>";
?>