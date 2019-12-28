<?php

class MySqlBasic{

	protected static $dbc;
	
	private static $echoQuery = false; // debug function
	private static $disableEscape = false; 
	
	public static function setDbc($dbc)
	{
		self::$dbc = $dbc;
	}
	
	public static function getDbc()
	{
		return self::$dbc;
	}
	
	public static function echoQuery()
	{
		self::$echoQuery = true;
	}
	
	public static function disableEscape($s = true)
	{
		self::$disableEscape = (bool) $s;
	}
	
	public static function connect($host, $user, $pass, $dbase)
	{
		$dbc = @mysql_connect($host, $user, $pass);
		
		if(!$dbc){
			trigger_error("Kan geen verbinding maken met mySQL: $user@$host\n" . mysql_error(), E_USER_ERROR);
		}
		
		mysql_query("SET NAMES 'utf8'", $dbc);
	
		if($dbase){
			self::setDataBase($dbase);
		}
		
		self::$dbc = $dbc;
	}
	
	public static function setDataBase($dbase)
	{
		$succes = @mysql_select_db($dbase);
		
		if(!$succes){
			trigger_error("Kan geen verbinding maken met de gekozen database '$dbase'", E_USER_ERROR);
		}
	}
	
	
	public static function query($q)
	{
		if(self::$echoQuery)
		{
			echo $q;
		}
	
		$qr = mysql_query($q, self::$dbc);
		
		if(!$qr)
		{
			$e = mysql_error(self::$dbc);
			throw new Exception("\nFout in Query:\n$q\n\n$e\n\n", E_USER_ERROR);
		}
		
		return $qr;
	}
	
	public static function freeResult($qr)
	{
		mysql_free_result($qr);
	}
	
	public static function insertId()
	{
		return mysql_insert_id(self::$dbc);
	}
	
	public static function affectedRows()
	{
		return mysql_affected_rows(self::$dbc);
	}
	
	public static function result($qr, $row, $field = NULL)
	{
		return mysql_result($qr, $row, $field);	
	}
	
	public static function fetchRow($qr)
	{
		return mysql_fetch_row($qr);	
	}
	
	public static function fetchAssoc($qr)
	{
		return mysql_fetch_assoc($qr);	
	}
	
	public static function numRows($qr)
	{
		return mysql_num_rows($qr);
	}
	
	public static function numFields($qr)
	{
		return mysql_num_fields($qr);
	}
	
	public static function multiFetchAssoc($qr)
	{
		$data = array();
		
		while($pdata = mysql_fetch_assoc($qr)){
			$data[] = $pdata;
		}
		
		return $data;
	}
	
	public static function fieldNameArray($qr)
	{
		$numFields = mysql_num_fields($qr);
		
		$fieldNames = array();
		
		for($i = 0; $i < $numFields; $i++)
		{
			$fieldNames[] = mysql_field_name($qr, $i);
		}
		
		return $fieldNames;
	}
	
	protected static function log($str)
	{
		//$fileName = Dir::logs . "mysql.log";
		//file_put_contents($fileName, $str . ";\r\n", FILE_APPEND);
	}
}


?>