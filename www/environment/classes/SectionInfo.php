<?php

class SectionInfo
{
	protected static $children = array(); // tijdelijke array
	protected static $sections = array();
	
 	
	public static function initialize()
 	{
 		$time = microtime(true);
 		
 		$data = MySql::select("SELECT * FROM u_sections LEFT JOIN u_cores USING(ID) ORDER BY ID = '" . Settings::get("homePageID") . "' DESC");
 		
 		// dit is jammer: 
 		// ik gebruik nu elke keer de ID als key
 		// kan dit ook op een stricte manier?

 		foreach($data as $row)
 		{
 			self::$children[$row["ID"]] = array();
 		}
 		
 		foreach($data as $row)
 		{	
 			if($row["ID"] == Settings::get("homePageID")) continue;
 			if(! isset (self::$children[$row["parent"]]) ) throw new Exception("Section '{$row["ID"]}' heeft onbekende parent '{$row["parent"]}'");
 			self::$children[$row["parent"]][] = $row;
 		}
 		
 		$homeRow = $data->getRow(0);
  		$hirarchy = self::buildHirarchy($homeRow); 
 		 		
 		$Root = new SectionInfo_Root($hirarchy["data"], $hirarchy["children"]);
 	}
 	
 	protected static function buildHirarchy($row)
 	{
 		$hirarchy = array(
 			"data" => $row,
 			"children" => array()
 		); 
 		
 		foreach(self::$children[$row["ID"]] as $child)
 		{
 			
 			$hirarchy["children"][] = self::buildHirarchy($child);
 		}
 		
 		return $hirarchy;
 	}
  	
  	public static function add($ID, SectionInfo_Abstract $Object)
  	{
  		if(isset(self::$sections[$ID])) throw new Exception("Tried to overwrite section '$ID'");
  		self::$sections[$ID] = $Object;
  	}
  	
 	public static function get($ID)
 	{
 		if(isset(self::$sections[$ID]))
 		{
 			return self::$sections[$ID];
 		}
 		else
 		{
 			throw new Exception("Tried to get unknown section $ID");
 		}
 	}	
}