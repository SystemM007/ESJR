<?php
class Clock
{
	/*
	public static $clocks = array();
	
	
	
	public function __construct($name = NULL)
	{
		if(!$name) $name = uniqid();
		self::$clocks[] = $this;
		
		$this->startTime = microtime(true);
		
		return $name;
	}
	*/
	protected $startTime = 0;
	
	public function __construct()
	{
		return $this->startTime = microtime(true);
	}
	
	
	public function finish()
	{
		return (microtime(true) - $this->startTime);
	}
	
	
}
?>