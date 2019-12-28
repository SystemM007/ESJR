<?php

class FunctionLoader
{
	public function __construct($path)
	{
		foreach(glob($path . "*.php") as $functionFile)
		{
			if(function_exists(basename($functionFile, ".php"))) continue;
			include($functionFile);
		}
	}
}