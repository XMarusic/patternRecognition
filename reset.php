<?php
	$empty = '[]';
	include 'url.php';

	$total_reset = false;

	if($total_reset === true){
		file_put_contents($history, $empty);
		file_put_contents($songList, $empty);
		file_put_contents($song_names, $empty);
		file_put_contents($sim_matrix, $empty);
		file_put_contents($duration, $empty);
	}

	$Lastsrno = 0;
	include 'getFiles.php';

	if($total_reset === true)
		$srno = 0;
	else $srno = $Lastsrno;

	include 'createDuration.php';
	include 'createMatrix.php';
?>