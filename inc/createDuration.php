<?php
	
$dictionary = json_decode(file_get_contents('/songListtemp.json'), true);;
$number = sizeof($dictionary);

$durationArray = array();

for($i = 0; $i<$number; $i++)
	array_push($durationArray, 0.00);

file_put_contents('duration.json', json_encode($durationArray));

?>