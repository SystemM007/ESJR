<?php

class MySql extends MySqlBasic{

	private static $disableEscape = false; 
	
	protected static $returnDataSet = true;
	

	public static function disableEscape($s = true)
	{
		self::$disableEscape = (bool) $s;
	}
	
	public static function returnDataSet($set = true)
	{
		self::$returnDataSet = $set;
	}	
	
	public static function select($sq)
	{
		$q = self::decodeSelect($sq);
		$qr = self::query($q);
		$data = self::multiFetchAssoc($qr);
		
		if(self::$returnDataSet)
		{
			$fieldNames = self::fieldNameArray($qr);
			$data = new DataSet($data, $fieldNames);
		}
		
		self::freeResult($qr);
		
		return $data;
	}
	
	public static function selectRow($sq)
	{
		$q = self::decodeSelect($sq);
		$qr = self::query($q);
		
		if(($n = self::numRows($qr)) > 1)
		{
			throw new Exception("Query selecteerde meer dan 1 rij ($n) bij selectRow: <br />$q");
		}
		
		$row = self::fetchAssoc($qr);
		
		self::freeResult($qr);
		
		return $row;
	}
	
	public static function selectValue($sq)
	{
	
		$q = self::decodeSelect($sq);
		$qr = self::query($q);
		
		if(($f = self::numFields($qr)) != 1)
		{
			throw new Exception("Query selecteerde meer dan kolom ($n) bij selectValue: <br />$q");
		}
		
		switch ($n = self::numRows($qr))
		{
			case 0 : return NULL;
			break;
			
			case 1 : return self::result($qr, 0);
			break;
			
			default : throw new Exception("Query selecteerde meer dan 1 rij ($n) bij selectValue: <br />$q");
		}
	}
	
	public static function limitedSelect(array $sq)
	{
		if(strpos((string) $sq["limit"], ",") !== false) trigger_error("Bij een limited select moet sq[limit] een integer zijn of leeg zijn". E_USER_ERROR);
		$sq["limit"] = ((int) $sq["limit"]);
	
		if($sq["limit"])
		{
			$sqNoLimit = array_merge($sq, array("limit"=>false));
			$numItems = self::numRowsSelect($sqNoLimit);
			$numRest = $numItems - $sq["limit"];
		}
		else $numRest = 0;

		$data = self::select($sq);
		
		return array($data, $numRest);
	}
	
	public static function pageSelect($sq)
	{
		if(strpos((string) $sq["limit"], ",") !== false) trigger_error("Bij een page select moet sq[limit] een integer zijn of leeg zijn". E_USER_ERROR);
		if(!$sq["limit"]) trigger_error("Bij een page select moet een limit worden gegeven", E_USER_ERROR);
		//if(!isset($sq["pageNumber"])) trigger_error("Bij een page select moet er een pageNumer worden gegeven", E_USER_ERROR);
		
		$pageNumber = $sq["pageNumber"] ? $sq["pageNumber"] : 1;
		$numOnPage = (int) $sq["limit"];
		
		$offset = $numOnPage * ($pageNumber - 1);
		$sq["limit"] = "$offset, $numOnPage";
		
		$data = self::select($sq);
		
		$sqNoLimit = array_merge($sq, array("limit"=>false));
		$numItems = self::numRowsSelect($sqNoLimit);
		
		return array($data, $numItems);
	}
	
	public static function insert($sq)
	{
		$q = self::decodeInsert($sq);	
		$qr = self::query($q);
		return self::insertId();
	}
	
	public static function update($sq)
	{
		$q = self::decodeUpdate($sq);
		self::query($q);
	}
	
	public static function increment($sq)
	{
		$q = self::decodeIncrement($sq);
		self::query($q);
	}	
	
	public static function delete($sq)
	{
		$q = self::decodeDelete($sq);
		$qr = self::query($q);
	}
	
	public static function numRowsSelect($sq)
	{
		$q = self::decodeNumRowsSelect($sq);
		$qr = self::query($q);
		return self::numRows($qr);
	}
	
	
	// decodeer functies
	
	public static function escape($str)
	{
		if(self::$disableEscape){
			return $str;
		}
		else{
			return mysql_real_escape_string($str, self::$dbc);
		}
	}


	private static function decodeSelect($sq){
	
		if(!is_array($sq)){
			return $sq;
		}
		
		extract($sq);
		
		if(!$select){
			throw new Exception("Het is verplicht in een SQ een waarde op te geven voor select: ". var_export($sq,true). "");
		}
		
		if(!$from){
			throw new Exception("Het is verplicht in een SQ een waarde op te geven voor from");
		}
		
		$q = "";
		$q .= "SELECT ";
		$q .= is_array($select) ? implode(", ", $select) : $select;
		$q .= " \nFROM ";
		$q .= is_array($from) ? implode(", ", $from) : $from;
		$q .= "";
		
		if($join)
		{
			// als er niet meerdere joins in een array zijn gegeven, maar
			// enkel de eerste join, alsnog in een array met lengte 1 zetten
			if(! is_array($join) || ! isset($join[0])) $join = array($join);
			
			foreach($join as $singleJoin)
			{
				// shortcut voor joinen op ID -> gewoon alleen de naam van de tabel geven!
				if(is_string($singleJoin)) $singleJoin = array("table" => $singleJoin, "using" => "ID");
				
				if($singleJoin["left"] || $singleJoin["right"]) throw new Exception("Kappen hiermee! JOIN verkeerd gegeven");
				
				switch($singleJoin["type"])
				{
					case "right" : 
						$q .= " \nRIGHT JOIN"; 
					break;
					case "left" : 
					default: 
						$q .= " \nLEFT JOIN ";
				}
				
				if(!$singleJoin["table"]) throw new Exception("Geen table gevonden in JOIN");
				$q .= "" . $singleJoin["table"] . " ";
				
				if($singleJoin["using"]) $q .= " USING (" . $singleJoin["using"] . ")";
				elseif($singleJoin["on"]) $q .= " ON " . $singleJoin["on"] . "";
				else throw new Exception("Join bevatte geen on of using");
			}
		}
		
		$q .= $where ? " \nWHERE $where" : "";
		$q .= $group ? " \nGROUP BY $group" : "";
		$q .= $order ? " \nORDER BY $order": "";
		$q .= $limit ? " \nLIMIT $limit" : "";

		return $q;
		
	}
	
	private static function decodeNumRowsSelect($sq)
	{
		if(!$sq["select"]) $sq["select"] = "*";
		
		return self::decodeSelect($sq);
	}
	
	private static function decodeInsert($sq){
	 
	 	if(!is_array($sq)){
			return $sq;
		}
		
		if(!$sq["table"]){
			throw new Exception("Het is verplicht in een SQ een waarde op te geven voor table");
		}
		
		if(is_array($sq["values"])){
			$values = array();
			$fields = array();
			foreach($sq["values"] as $field => $value){
				// het is niet nodig lege velden in te voeren bij een insert: deze zullen hun standaard waarde aannemen
				if(strlen($value) == 0 || strlen($field) == 0){ 
					continue;
				}
			
				$value = self::escape($value);
				$values[] = "'" . $value . "'";
				$fields[] = "" . $field . "";
			}
			
			$sq["fields"] = implode(", ", $fields);
			$sq["values"] = implode(", ", $values);
		}
		
		// geen template: wel lekker snel!
		$q = "INSERT INTO ". $sq["table"] ." (". $sq["fields"] .") VALUES (". $sq["values"] .")";
		
		return $q;
		
	}
	
	private static function decodeUpdate($sq)
	{
	 	if(!is_array($sq)) return $sq;
		
		extract($sq);
		
		if(!$table) trigger_error("Het is verplicht in een SQ een waarde op te geven voor table " . vds($sq), E_USER_ERROR);
		if(!is_array($values) || !$values) throw new Exception("Het is verplicht in een SQ een waarde op te geven voor values " . vds($values), E_USER_ERROR);
	
		$set = array();
		foreach($values as $field => $value)
		{
			$value = self::escape($value);
			$set[] = "`$field` = '$value'";
		}
		$set = implode(", ", $set);
		
		$q = "";
		$q .= "UPDATE $table SET $set";
		$q .= $where ? " WHERE " . $where : "";
		$q .= $limit ? " LIMIT " . $limit : "";

		return $q;
	}
	
	private static function decodeIncrement($sq)
	{
	 	if(!is_array($sq)) return $sq;
		
		extract($sq);
		
		if(!$table) throw new Exception("Het is verplicht in een SQ een waarde op te geven voor table " . vds($sq), E_USER_ERROR);
		if(!is_array($values) || !$values) throw new Exception("Het is verplicht in een SQ een waarde op te geven voor values " . vds($values), E_USER_ERROR);
	
		$set = array();
		foreach($values as $field => $value)
		{
			$value = intval($value);
			$valueAbs = abs($value);
			$value = $value >= 0 ? "+ $valueAbs" : "- $valueAbs";
			$set[] = "`$field` = `$field` $value";
		}
		$set = implode(", ", $set);
		
		$q = "";
		$q .= "UPDATE $table SET $set";
		$q .= $where ? " WHERE " . $where : "";
		$q .= $limit ? " LIMIT " . $limit : "";

		return $q;
	}
	
	private static function decodeDelete($sq)
	{
	 	if(!is_array($sq)) return $sq;
		
		extract($sq);
		
		if(!$table) throw new Exception("Het is verplicht in een SQ een waarde op te geven voor table " . vds($sq), E_USER_ERROR);
		if(!$table) throw new Exception("Het commande DELETE uitvoeren zonder een waarde voor WHERE is geblokkeerd", E_USER_ERROR);
		if(!isset($limit)) throw new Exception("Het commande DELETE uitvoeren zonder een waarde voor LIMIT is geblokkeerd", E_USER_ERROR);
		
		$q = "";
		$q .= "DELETE FROM $table";
		$q .= $where ? " WHERE " . $where : "";
		$q .= $limit ? " LIMIT " . $limit : "";
		
		return $q;
	}
	
	/*
	* DEPRICIATED!!!!!!!!!!!!!!!!!!!!!!!
	* Dit is een ranzige manier om zelf de query te lezen en 
	* aan de hand daarvan de kolommen te bepalen
	* Werkend (hulde), maar natuurlijk niet voor zaken als SELECT *
	*
	* Gebruik nu gewoon MySql::fieldNameArray($qr)
	*
	
	private static function getColumns($sq)
	{	
		// ook string query's uit elkaar trekken
		
		if(is_array($sq))
		{
			$select = $sq["select"];
			
			if(!is_array($select)) $select = explode(",", $select);
		}
		else
		{
			$pattern = "/SELECT(.*)(FROM|WHERE)(.*)/si";
			preg_match($pattern, $sq, $results);
			$select = $results[1];
			$select = explode(",", $select);
			foreach ($select as $key => $val) $select[$key] = trim($val);
		}
		
		if(!is_array($select)) throw new Exception("Donders, select is geen array");
		
		// zorgen dat velden als 
		// COUNT(*) AS tel, table.field
		// ook netjes in de Matrix komen als
		// tel, field
			
		foreach ($select as $key => $value)
		{
			$select[$key] = self::getColumnName($value);
		}
		
		return $select;
	}
	*/
	public static function getColumnName($sqlFieldName)
	{
		if(($offset = stripos($sqlFieldName, " AS ")) !== false)
		{
			$sqlFieldName = trim(substr($sqlFieldName, $offset + 4));
		}
		elseif(($offset = stripos($sqlFieldName, ".")) !== false)
		{
			$sqlFieldName = substr($sqlFieldName, $offset + 1);
		}
		
		return $sqlFieldName;
	}
}


?>