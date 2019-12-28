<?php
/* 
* Migrated 16 feb 2015 to mysqli, all methods kept their original names
*/

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
		//$dbc = @mysql_connect($host, $user, $pass);
		$dbc = new mysqli($host, $user, $pass, $dbase);
		
		if(!$dbc){
			trigger_error("Kan geen verbinding maken met mySQL: $user@$host\n" . mysqli_error($dbc), E_USER_ERROR);
		}
		
		//mysql_query("SET NAMES 'utf8'", $dbc);
		$dbc->set_charset("utf8");
	
		//if($dbase){
		//	self::setDataBase($dbase);
		//}
		
		self::$dbc = $dbc;
	}
	
	public static function setDataBase($dbase)
	{
		$succes = self::$dbc->select_db($dbase);
		
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
	
		// $qr = mysql_query($q, self::$dbc);
		$qr = self::$dbc->query($q);
		
		if(!$qr)
		{
			//$e = mysql_error(self::$dbc);
			// $e = self::$dbc->$error; 		// note, used procedural style, because otherwise empty results will raise an error
			$e = mysqli_error(self::$dbc); 
			
			throw new Exception("\nFout in Query:\n$q\n\n$e\n\n", E_USER_ERROR);
		}
		
		return $qr;
	}
	
	public static function freeResult($qr)
	{
		//mysql_free_result($qr);
		$qr->free();
	}
	
	public static function insertId()
	{
		//return mysql_insert_id(self::$dbc);
		// return self::$dbc->$insert_id; 		// note, used procedural style, because otherwise empty results will raise an error
		return mysqli_insert_id(self::$dbc);
	}
	
	public static function affectedRows()
	{
		//return mysql_affected_rows(self::$dbc);
		return self::$dbc->$affected_rows;
	}
	
	public static function result($qr, $row, $field = 0)
	{
		// return mysql_result($qr, $row, $field);	
		// http://php.net/manual/en/class.mysqli-result.php#109782
		$qr->data_seek($row); 
    	$datarow = $qr->fetch_array(); 
	    return $datarow[$field];
	}
	
	public static function fetchRow($qr)
	{
		//return mysql_fetch_row($qr);	
		return $qr->fetch_row();
	}
	
	public static function fetchAssoc($qr)
	{
		//return mysql_fetch_assoc($qr);	
		return $qr->fetch_assoc();
	}
	
	public static function numRows($qr)
	{
		//return mysql_num_rows($qr);
		//return $qr->$num_rows; 		// note, used procedural style, because otherwise empty results will raise an error
		return mysqli_num_rows($qr);
	}
	
	public static function numFields($qr)
	{
		//return mysql_num_fields($qr); 		// note, used procedural style, because otherwise empty results will raise an error
		return mysqli_field_count(self::$dbc);
	}
	
	public static function multiFetchAssoc($qr)
	{
		$data = array();
		
		//while($pdata = mysql_fetch_assoc($qr)){
		while($pdata = $qr->fetch_assoc()){
			$data[] = $pdata;
		}
		
		return $data;
	}
	
	public static function fieldNameArray($qr)
	{
		//$numFields = mysql_num_fields($qr);
		//$numFields = self::$dbc->$field_count; 		// note, used procedural style, because otherwise empty results will raise an error
		$numFields = mysqli_field_count(self::$dbc);
		
		$fieldNames = array();
		
		for($i = 0; $i < $numFields; $i++)
		{
			//$fieldNames[] = mysql_field_name($qr, $i);
			$fieldNames[] = $qr->fetch_field_direct($i)->name;
		}
		
		return $fieldNames;
	}
	
	protected static function log($str)
	{
		//$fileName = Dir::get()->logs. "mysql.log";
		//file_put_contents($fileName, $str . ";\r\n", FILE_APPEND);
	}
}


?>