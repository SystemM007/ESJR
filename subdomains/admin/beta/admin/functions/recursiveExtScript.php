<?php

function recursuveExtScript($dir, $uri, $limit = 10)
{
	$scripts = "";
	foreach(recursiveFileLoader($dir, "js", $limit) as $file)
	{
		$fileRel = substr($file, strlen($dir));
		$scripts .= new Fragment_Tag_ExternScript($uri . $fileRel);
	}
	return $scripts;
}