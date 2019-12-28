<?php

$timeProfileRegister = array();

function timeProfileStart($name)
{
	global $timeProfileRegister;
	$timeProfileRegister[$name] = microtime(true);
}

function timeProfileStop($name)
{
	global $timeProfileRegister;
	$fileName = dirname(__FILE__) . "/files/profiler/$name.dat";
	$duration = microtime(true) - $timeProfileRegister[$name];
	$data = "$duration\r\n";
	
	file_put_contents($fileName, $data, FILE_APPEND);
	
	unset($timeProfileRegister[$name]);
}