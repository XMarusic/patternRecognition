<?php
	include_once 'inc/loadFiles.php';
	include_once ROOT.'/url.php';

	$dictionary = loadFromFile();
	$size = sizeof($dictionary);

	$sim_arr = array();

	for ($i=0; $i < $size; $i++) { 
		$t_sim_arr = array();
		for ($j=0; $j < $size; $j++) { 
			array_push($t_sim_arr, 0);
		}
		$sim_arr[] = $t_sim_arr;
	}

	if(file_put_contents($sim_matrix, json_encode($sim_arr)))
		echo "Success<br>";
?>