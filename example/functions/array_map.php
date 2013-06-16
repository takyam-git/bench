<?php
$numbers = range(0,1000);
$result = array_map(function($number){
	return sqrt($number);
}, $numbers);