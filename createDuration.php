<?php

include_once 'inc/loadFiles.php';
include_once ROOT.'/url.php';

$dictionary = loadFromFile();
$size = sizeof($dictionary);

$durationArray = array();
for($i = 0; $i < $size; $i++){
	$durationArray[] = array("key" => $i, "expected_duration" => 0.00, "duration" => 0.00);
}

if(file_put_contents($duration, json_encode($durationArray)))
	echo "Success<br>";

?>