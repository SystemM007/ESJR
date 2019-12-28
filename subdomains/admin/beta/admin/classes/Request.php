<?php

class Request extends RequestBasic
{
	// arrays met userinput	
	public static $Input = array();
	public static $instanceId = 0;
	
	public static function init()
	{
	
		parent::initBasic();
	
		// read POST
		// ja ik overschrijf de bestaande $Post, om compabiliteits redenen
		self::$Post = self::readJsonPostField("query");
				
		// read INPUT
		self::$Input = self::readJsonPostField("input");
		
		// set Instance ID
		self::$instanceId = isset($_POST["instanceId"]) ? $_POST["instanceId"] : NULL;
	}
}