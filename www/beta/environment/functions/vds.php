<?php

function vds($v, $die = false)
{	
	ob_start();
	var_dump($v);
	$d = ob_get_contents();
	ob_clean();
	
	$d = htmlentities($d);
	$d = str_replace(" ", "&nbsp;&nbsp;&nbsp;", $d);
	$d = str_replace("[\"", "\n[\"", $d);
	$d = nl2br($d);
	$d = "<br />\n<br />\n$d<br />\n<br />\n";
	
	if($die){
		die($d);
	}
	
	return $d;
}