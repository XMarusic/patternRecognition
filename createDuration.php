<?php

include 'inc/loadFiles.php';

$dictionary = loadFromFile();
$size = sizeof($dictionary);

$durationArray = array();
for($i = 0; $i < $size; $i++){
	$durationArray[] = array("key" => $i, "expected_duration" => 0.00, "duration" => 0.00);
}

file_put_contents('inc/duration.json', json_encode($durationArray));

?>