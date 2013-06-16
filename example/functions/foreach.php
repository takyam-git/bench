<?php
$numbers = range(0,1000);
$result = array();
foreach($numbers as $key => $number){
	$result[$key] = sqrt($number);
}