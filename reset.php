<?php
	$empty = '[]';
	include 'url.php';

	file_put_contents($history, $empty);
	file_put_contents($songList, $empty);
	file_put_contents($song_names, $empty);

	include 'getFiles.php';
	include 'createDuration.php';
	include 'createMatrix.php';
?>