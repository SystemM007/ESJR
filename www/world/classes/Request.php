<?php

class Request extends RequestBasic
{
	public static $File;
	public static $AjaxPost;

	public static function init()
	{
		parent::initBasic();
		
		// init uploaded file
		self::$File = self::checkUpload();
		// read Ajax Post
		self::$AjaxPost = self::readJsonPostField("query");
		
		self::startPage();
	}
	
	private static function startPage()
	{
		/*
			Eerst wordt de normale $_GET bekeken.
			Deze is NIET door het publiek gegeven, want dat wordt herschreven
			Deze GET waarden kunnen dus alleen uit de .htaccess komen
		*/
		if($_GET["specialPage"] == "403") self::give403();
		if($_GET["specialPage"] == "404") self::give404();
		
		
		$section = self::searchSection();
		self::startSection($section["ID"], $section["siteModule"], $section["paramAllowed"]);
	}
	
	private static function searchSection()
	{
		// Beginnen met homepage selecteren
		$section = self::getHomeSection();
		
		// Zoeken naar onderliggende afdelingen voor iedere stap in de URL
		while( self::$Path[0] )
		{
			/*
				Zoek in de database of er een section is die
				voldoet aan de gegeven urlName en die ligt onder de
				vorige geselecteerde section
			*/
			$search = self::getSearchSection($section["ID"], self::$Path[0]);
						
			if($search)
			{
				// gevonden, dit stuk van de URL wordt gestript
				// en dit wordt de huidige section
				array_shift(self::$Path); 
				$section = $search;
			}
			
			if(! $search || !$section["childrenAllowed"] )
			{
				// als er niet gevonden is, 
				// >> OF er onder het gevondene geen kinderen liggen mogen
				// dan moet er sowieso worden gestopt met verder zoeken
				break;
			}
		}
		
		return $section;
	}
	
	private static function startSection($ID, $siteModule, $paramAllowed)
	{
		if(self::$Path[0] && !$paramAllowed)
		{
		 	// er is nog een stukje URL over dat niet als section gevonden werd
			// en er zijn geen parameters toegestaan: foute boel
			// deze aanvraag is dus ongeldig, de pagina bestaat niet
			self::give404();
		}
		
		$class = "Section_" . $siteModule;
		
		if(! class_exists($class) )
		{
			throw new Exception("siteModule '$class' werd niet gevonden!");
		}
		
		if(! is_subclass_of($class, "Section_Abstract") )
		{
			throw new Exception("siteModule '$class' is geen subclasse van Section_Abstract");
		}
	
		$Section = new $class($ID);
		
		$Section->finish();
	}
	
	private static function getHomeSection()
	{
		return self::getSection("ID = '" . Settings::get("homePageID") . "'");
	}
	
	private static function getSearchSection($parent, $urlName)
	{
		/*
		 * Urlname moet worrden geencodeerd, want zo staan de urls in de database
		 */
		$urlName = rawurlencode($urlName);
		$urlName = MySql::escape($urlName);
		return self::getSection("u_sections.urlName = '$urlName' AND u_cores.parent = '$parent' AND u_cores.enabled = '1'");
	}
	
	private static function getSection($where)
	{
		return MySql::selectRow(array(
			"select" => array("u_cores.ID", "u_cores.childrenAllowed", "u_sections.siteModule", "u_sections.paramAllowed"),
			"from" => "u_sections",
			"join" => array("table" => "u_cores", "using" => "ID"),
			"where" =>  $where
		));
	}
	
	public static function exceptionHandler(Exception $Exception)
	{
		$err = "";
		$err .= $Exception->getMessage() . "\n";
		$err .= "Code:\t " . $Exception->getCode() . "\n";
		$err .= "File:\t " . $Exception->getFile() . "\n";
		$err .= "Line:\t " . $Exception->getLine() . "\n";	
		$err .= backtraceString($Exception->getTrace())  . "\n";
		
		header("HTTP/1.1 500 Internal Server Error");
		
		if(ini_get("html_errors"))
		{
			$err = nl2br(htmlspecialchars($err));
		}
		
		die($err);
	}

}