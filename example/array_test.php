<?php
require_once(__DIR__ . '/../lib/Bench/Bench.php');
$base_dir = dirname(__FILE__).'/functions';
$bench = new Bench($base_dir.'/array_map.php', $base_dir.'/foreach.php');
echo $bench->run();