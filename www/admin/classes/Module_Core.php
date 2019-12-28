<?php


abstract class Module_Core extends Module_Life
{
	protected $Core;
	
	public function __construct(Core $Core)
	{
		$this->Core = $Core;
	}
	
	final public function getID()
	{
		return $this->Core->ID;
	}
	
	final public function getModuleName()
	{
		$class = explode("_", get_class($this));
		array_pop($class);
		array_shift($class);
		
		return implode("_", $class);
	}
}