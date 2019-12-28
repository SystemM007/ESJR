<?php

class Template extends Template_Processor
{
	protected static $paths = array();

	public function __construct($templateName, array $singleFill = array())
	{
		parent::__construct($this->getTemplate($templateName));
		
		if($singleFill) $this->singleFill($singleFill);
	}
	
	protected function getTemplate($templateName)
	{
		foreach(self::$paths as $path)
		{
			$file = $path . $templateName . ".tpl-ser";
			if(file_exists($file))
			{
				$Root = unserialize(file_get_contents($file));
				if(! $Root instanceof Template_Root_Element) throw new Exception("Template '$templateName' geeft geen Template_Root_Element");
				return $Root;
			}
		}
		
		throw new Exception("Unknown template '$templateName'");
	}
	
	public static function addPath($path)
	{
		self::$paths[] = $path;
	}
}