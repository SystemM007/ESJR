<?php

class Settings{

	private static $cache = array();
	private static $updated = array();

	/*
	public static function get($setting)
	{
		if(isset(self::$cache[$setting])) return self::$cache[$setting];
		
		$value = MySql::selectValue(array(
			"select" => "value",
			"from" => "u_settings",
			"where" => "`name` = '$setting'"
		));
				
		if(!isset($value)){
			throw new Exception("Onbekende setting '$setting' opgevraagd", E_USER_ERROR);
		}
		return self::$cache[$setting] = $value;
	}
	
	public static function update($setting, $value)
	{
		try{
			MySql::update(array(
				"table" => "u_settings",
				"where" => "`name` = '$setting'",
				"values" => array(
					"value" => $value
				)
			));
		}
		catch(Exception $e)
		{
			throw new Exception("Er werd geprobeerd een onbekende setting '$setting' te updaten", E_USER_ERROR);
		}
		
		self::$cache[$setting] = $value;
	}
	*/
	
	public static function get($setting)
	{
		if(!self::$cache) self::updateCache();
		
		if(!isset(self::$cache[$setting]))	throw new Exception("Onbekende setting '$setting' opgevraagd");

		return self::$cache[$setting];
	}
	
	public static function update($setting, $value)
	{
		if(!self::$cache) self::updateCache();
		if(!isset(self::$cache[$setting])) throw new Exception("Onbekende setting '$setting' gepoogd te updaten");
		
		self::$cache[$setting] = $value;
		
		if(!self::$updated) register_shutdown_function(array("Settings", "updateDatabase"));
		if(!isset(self::$updated[$setting])) self::$updated[$setting] = true;
		
		return $value;
	}	
	
	
	
	protected static function updateCache()
	{
		if(!self::$cache)
		{
			foreach(MySql::select("SELECT name, value FROM u_settings") as $setting) self::$cache[$setting["name"]] = $setting["value"];
		}
	}
	
	public static function updateDatabase()
	{
		foreach(array_keys(self::$updated) as $setting)
		{
			MySql::update(array(
				"table" => "u_settings",
				"where" => "`name` = '". MySql::escape($setting) ."'",
				"values" => array(
					"value" => self::$cache[$setting]
				)
			));
		}
	}
}

?>