<?php

function __autoload($className){Autoload::loadClass($className);}

class Autoload
{
	protected static $paths = array();

	private function __construct(){}
	
	public static function loadClass($className)
	{
		if(class_exists($className, false)) throw new Exception("Attempt to load defined class '$className'");
		
		foreach(self::$paths as $path => $postfix)
		{
			$file = $path . $className . $postfix;
			if(file_exists($file))
			{
				require_once($file);
				return;
			}
		}
	}
	
	public static function preloadClass($className)
	{
		// een versie die veilig kan worden aangeroepen!
		if(class_exists($className, false))
		{
			return;
		}
		self::loadClass($className);
	}
	
	public static function addPath($path, $postfix = ".php")
	{
		self::$paths[$path] = $postfix;
	}
}