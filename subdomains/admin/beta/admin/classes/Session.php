<?php
/*
Deze klasse geeft een substituut voor de standaard session van PHP
De data wodt in een database opgeslagen.

Een tweede functie is dat er instances kunnen worden gemaakt. Zo kan ieder venster apart ook een eigen reeks variabelen opslaan.
Bij een AJAX app ligt het voor de hand de instanceId via Post door te spelen voor iedere request.

 
De klasse verzorgt zelf geen functionaliteit voor het doorspelen van de instanceId
Daarom moet deze bij het initialiseren worden gegeven en wordt deze slechts geretourneerd bij het creeren van een nieuwe instance
*/

class Session
{
	public static $global = array();
	public static $instance = array();
	
	private static $sessionId;
	private static $instanceId;
	const maxLifeTime = 86400;
	
	public static function getInstanceId(){return self::$instanceId; }
	public static function getSessionId(){return self::$sessionId; }
	
	public static function init($instanceId = false)
	{
		self::$sessionId = $_COOKIE[ini_get("session.name")];
		self::$instanceId = $instanceId;
		
		if(!self::$sessionId)
		{
			self::create();
		}
		else
		{
			self::$global = self::read();
			if(self::$instanceId) self::$instance = self::read(self::$instanceId);
		}
		
		self::gc();
	}
	
	public static function createInstance()
	{
		$highestInstance = MySql::selectValue(array(
			"select" => "MAX(instance)",
			"from" => "u_sessions",
			"where" => "`salt` = '" . self::$sessionId ."'",
		));
		
		self::$instanceId = ++$highestInstance;
		
		self::insert(self::$instanceId);
		
		return self::$instanceId;
	}
	
	public static function finish()
	{
		if(!self::$sessionId) return;
		self::write(self::$global);
		if(self::$instanceId) self::write(self::$instance, self::$instanceId);
	}	
	
	public static function destroy()
	{
		self::delete();
		
		setcookie(ini_get("session.name"), "", time() - 42000, ini_get("session.cookie_path"), ini_get("session.cookie_domain"), ini_get("session.cookie_secure"));
		self::$sessionId = NULL;
	}
	
	private static function create()
	{		
		self::$sessionId = md5(uniqid());
		self::insert();
		setcookie(ini_get("session.name"), self::$sessionId, ini_get("session.cookie_lifetime"), ini_get("session.cookie_path"), ini_get("session.cookie_domain"), ini_get("session.cookie_secure"));
	}
	
	private static function insert($instance = 0)
	{
		MySql::insert(array(
			"table" => "u_sessions",
			"values" => array("salt" => self::$sessionId, "instance" => $instance, "lastAccess" => time())
		));
	}
	
	private static function read($instance = 0)
	{
		$data = MySql::selectValue(array(
			"select" => "data",
			"from" => "u_sessions",
			"where" => "`salt` = '" . self::$sessionId . "' AND `instance` = '$instance'"
		));
		if(is_string($data))
		{
			return unserialize($data);
		}
		else
		{
			// dit kan gebeuren wanneer de gebruiker wel een cookie heeft die echter niet in de database bestaat.
			self::insert($instance);
			return array();
		}
	}
	
	private static function delete()
	{
		MySql::delete(array(
			"table" => "u_sessions",
			"where" => "`salt` = '" . self::$sessionId . "'",
			"limit" => false
		));
	}
	
	private static function write($data, $instance = 0)
	{
		MySql::update(array(
			"table" => "u_sessions",
			"where" => "`salt` = '" . self::$sessionId . "' AND `instance` = '$instance'",
			"values" => array("data" => serialize($data), "lastAccess" => time())
		));
	}
	
	private static function gc()
	{
		MySql::delete(array(
			"table" => "u_sessions",
			"where" => "`lastAccess` < " . (time() - self::maxLifeTime),
			"limit" => false
		));
	}
}