<?php

function recursiveFileLoader($dir, $extention, $limit = 10)
{
	$files = glob($dir . "*" . $extention);
	if(!$files) return array();
	
	sort($files);
	foreach($files as $key => $file) if(is_dir($dir. $file)) unset($files[$key]);

	if($limit > 0)
	{
		$subDirs = glob($dir . "*", GLOB_ONLYDIR);
		if($subDirs)
		{
			sort($subDirs);
			foreach($subDirs as $subDir) $files = array_merge($files, recursiveFileLoader($subDir . "/", $extention, $limit-1));
		}
	}
	
	return $files;
}