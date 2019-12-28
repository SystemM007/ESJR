<?php

class History
{
	/*
		Offset is aantal stappen terug in de geschiedenis.
		Offset = 0 geeft huidige request
		Offset = 1 de vorige
		
	*/
	
	public static function count()
	{
		return  count(Session::$instance["history"]);
	}

	public static function get($offset)
	{
		if($offset < 0) throw new Exception("Offset mag niet negatief zijn", E_USER_ERROR);

		$key = count(Session::$instance["history"]) -1 - $offset;
		if($key < 0) return false;

		return Session::$instance["history"][$key];
	}
	
	public static function jumpTo($offset)
	{
		if($offset < 0) throw new Exception("Offset mag niet negatief zijn", E_USER_ERROR);
		
		$history = self::get($offset);
		
		if($offset == 0 && $history["ID"] == $_POST["ID"] && $history["Post"] == Request::$Post && $history["Input"] == Request::$Input)
		{
			throw new Exception("Eindeloze lus gedetecteerd: er wordt gesproken naar offset=0 in de geschiedenis, echter dat is de huidige pagina", E_USER_ERROR);	
		}

		// history n� de pagina verwijderen!
		// ook pagina ZELF verwijderen (maakt zichzelf opnieuw aan bij inititaliseren)
		self::clearHistory($offset);
		
		self::jump($history);
	}
	
	public static function clearHistory($offset)
	{
		if(self::count() == 0) return;
		
		if($offset < 0) throw new Exception("Offset mag niet negatief zijn", E_USER_ERROR);
		
		$i = self::count() - 1 - $offset;
	
		if($i < 0) throw new Exception("De offset was te groot: i=" . $i, E_USER_ERROR);
	
		while(isset(Session::$instance["history"][$i]))
		{
			unset(Session::$instance["history"][$i]);
			$i++;
		}
	}
	
	public static function clearAll()
	{
		self::clearHistory(self::count() -1);
	}
	
	public static function jumpToLatestGlobal()
	{
		if(count(Session::$global["history"]) == 0) return false;
		
		Session::$instance["history"] = Session::$global["history"];
		
		self::jumpTo(0);
		
		return true;
	}
	
	private static function jump($history)
	{
		Request::$File = array();
		Request::$Input = $history["Input"];
		Request::$Post = $history["Post"];
		
		$Core = new Core($history["ID"]);
		
		$Core->Page;
	}
	
	public static function make($name, Module_Abstract_Page $Page)
	{
		$history = array(
			"ID" => $Page->getID(),
			//"File" => Request::$File,
			"Input" => Request::$Input,
			"Post" => Request::$Post,
			"name" => $name,
		);
		$key = self::count();
		Session::$instance["history"][$key] = $history; 
		
		//globale kopie voor wanneer er een nieuw venster geopend wordt.
		Session::$global["history"] = Session::$instance["history"];
		
		return $key;
	}
	
	public static function change($key, $history)
	{
		Session::$instance["history"][$key] = array_merge(Session::$instance["history"][$key], $history);
	}
	
	public static function changeName($key, $name)
	{
		self::change($key, array("name"=>$name));
	}
}