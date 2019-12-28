<?php

class User{

	/*
		Levels zijn zo ingedeeld dat 
		0 - minste toegang
		1 - meer toegang
		2 - meer toegang
		enz...
		
		om dit te kunnen uitbreiden zijn de levels ALWAYS en ALWAYS_NO_LOGIN negatief!!
		in tegen stelling tot de oplopende getallen van de levels zijn de negatieve levels
		dus juist m��r toegangkelijk dan �lle positieve levels
	*/

	const USERLEVEL_NOT_LOGGED_IN = -1;
	const USERLEVEL_HIGHEST = 0; // aller krachtigste gebruiker
	const USERLEVEL_SUPERUSER = 1; // voor account jd
	const USERLEVEL_NORMAL = 2; // webbeheerders (van jd)
	const USERLEVEL_EASY = 3; // webfeut
	
	const ACCESSLEVEL_HIGHEST = 0;
	const ACCESSLEVEL_SUPERUSER = 1;
	const ACCESSLEVEL_NORMAL = 2;
	const ACCESSLEVEL_EASY = 3;
	const ACCESSLEVEL_ALWAYS = -1;
	const ACCESSLEVEL_ALWAYS_NO_LOGIN = -2;

	protected static $ID = NULL;
	public static $userName = "";
	public static $name = "";
	protected static $level = self::USERLEVEL_NOT_LOGGED_IN;


	public static function getID(){ return self::$ID; }
	public static function getUserName(){ return self::$userName; }
	public static function getName(){ return self::$name; }
	public static function getLevel(){ return self::$level; }
	
	public static function getLevelName($userLevel = NULL)
	{
		$userLevel = isset($userLevel) ? $userLevel : self::$level;
		
		$userLevelData = MySql::selectRow(array(
			"select" => "name",
			"from" => "u_adminUserTypes",
			"where" => "`userLevel` = '$userLevel'",
		));
		
		if(!count($userLevelData)) throw new Exception("UserLevel '$userLevel' niet gevonden!");
		
		return $userLevelData["name"];
	}
	
	public static function getAccessLevelName($accessLevel)
	{	
		$accessLevelData = MySql::selectRow(array(
			"select" => "name",
			"from" => "u_adminAccesslevels",
			"where" => "`accessLevel` = '$accessLevel'",
		));
		
		if(!count($accessLevelData)) throw new Exception("AccessLevel '$accessLevel' niet gevonden!");
		
		return $accessLevelData["name"];
	}

	public static function initDevelopper($loadDevelopper = null)
	{	
		self::$name = "Developper";
		self::$username = "developper";
		self::$level = isset($loadDevelopper) ? $loadDevelopper : self::USERLEVEL_HIGHEST;
	}

	public static function init()
	{
		if(!$_COOKIE["username"] || !$_COOKIE["password"]) return;
		$username = $_COOKIE["username"];
		$passwordh = $_COOKIE["password"];
				
		if(!$username || strlen($passwordh) != 32)
		{
			throw new Exception("Onjuist geformatteerde Authentication Cookie: $username, $passwordh");
		}
		
		$data = MySql::select(array(
			"select" => array("u_cores.ID", "u_cores.name", "u_adminusers.userName", "u_adminusers.password", "u_adminusers.userLevel"),
			"from" => "u_adminusers",
			"join" => array("table" => "u_cores", "using" => "ID"),
			"where" => "`userName` = '$username'"
		));
		
		//var_dump($data);
		
		if(!$data->count())
		{
			throw new Exception("Gebruikersnaam fout. Gebruikersnaam '$username' werd niet gevonden");
		}
		
		$userdata = $data->getRow(0);
		
		if($userdata["password"] != md5($passwordh) )
		{
			throw new Exception("Wachtwoord fout. Wachtwoord voor '$username' in Cookie is incorrct");
		}
		
		self::$ID =  $userdata["ID"];
		self::$userName =  $userdata["userName"];
		self::$name =  $userdata["name"];
		self::$level = $userdata["userLevel"];

	}
	
	public static function levelAllowed($accessLevel, $userLevel = NULL)
	{
		$userLevel = isset($userLevel) ? $userLevel : self::$level;
		
		if($accessLevel == self::ACCESSLEVEL_ALWAYS_NO_LOGIN)
		{
			return true;
		}
		elseif($userLevel == self::USERLEVEL_NOT_LOGGED_IN)
		{
			return false;
		}
		elseif($accessLevel == self::ACCESSLEVEL_ALWAYS)
		{
			return true;
		}
		else
		{
			return ($userLevel <= $accessLevel);
		}
	}
	
	public static function writeCookie($username, $password, $time = 0)
	{
		setcookie("username", $username, $time, "/");
		setcookie("password", $password, $time, "/");
	}
}