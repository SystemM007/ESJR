<?php

abstract class RequestBasic
{
	// request types
	const TYPE_AJAX = 1;
	const TYPE_GOOGLE = 2;
	// .. todo: meer types

	// arrays met userinput	
	public static $Post = array();
	public static $Get = array();
	public static $Path = array();
	public static $Type = 0;
	public static $File = Null;
	
	private static $uploadedFiles = array();
	
	// variabelen voor makkelijk werken met links
	public static $originalPath = "";
	public static $fullPath = ""; // deze versie is relatief tot de domein root en heeft altijd een trailing slash

	
	public static function initBasic()
	{
		// type
		self::checkType();
		// Post
		self::$Post = $_POST;
		// Get
		self::readGet();
		// Path
		self::readPath();
		// FILE
		self::checkUpload();
	}
	
	private static function checkType()
	{
		// typen requests vasatstellen
		$h = request_headers();
		
		if(isset($h["x-requested-with"]) && $h["x-requested-with"] == "XMLHttpRequest") self::$Type |= self::TYPE_AJAX;
		
		if(stripos($_SERVER['HTTP_USER_AGENT'], "Googlebot") !== false) self::$Type |= self::TYPE_GOOGLE;
				
		// nog wat settings aan de hand van de requests
		if(self::typeAjax()) ini_set("html_errors", false);
	}
	
	private static function readGet()
	{
		if($getString = parse_url($_SERVER["REQUEST_URI"], PHP_URL_QUERY))
		{
			foreach(explode("&", $getString) as $kvpair)
			{
				list($key, $value) = explode("=", $kvpair);
				if($key && $value) self::$Get = array_merge(self::$Get, array($key => $value));
			}
		}
	}
	
	private static function readPath()
	{	
		self::$originalPath = parse_url($_SERVER["REDIRECT_URL"], PHP_URL_PATH);
		
		if(self::$originalPath)
		{
			self::$Path = explode("/", self::$originalPath);
			
			array_shift(self::$Path); // eerste element is niets
			if(empty(self::$Path[count(self::$Path)-1]))
			{
				// laatste element is ook niets // hoort zo! dat betekent dat in der URL 
				// een trailing slash zat!
				array_pop(self::$Path);
			}
		}
		else
		{
			self::$Path = array();
		}
		
		self::$fullPath = "/" . implode("/", self::$Path) . "/";
		
	}
	
	// kan door child worden aangeroepen -----------------------------
	
	protected static function checkUpload($uploadField = "Filedata")
	{
		if(!$_FILES) return;
		
		switch ($_FILES["$uploadField"]["error"])
		{
			case UPLOAD_ERR_INI_SIZE:
				$error = "De grootte van het verstuurde bestand overscheidt de door het systeem gestelde van waarde '". maxUploadMB() ." MB";
			break;
			case UPLOAD_ERR_FORM_SIZE:
				$error = "De grootte van het verstuurde bestand overscheidt de waarde die gespecifieerd werd in het formulier van deze site.";
			break;
			case UPLOAD_ERR_PARTIAL:
				$error = "Upload werd niet voltooid.";
			break;
			case UPLOAD_ERR_NO_FILE:
				$error = "Er is geen bestand geüpload.";
			break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$error = "Tijdelijke opslag folder ontbreekt (fout op de server).";
			break;
		}

		if($error)
		{
			throw new Exception($error);
		}
		
		
//		$file["name"] = $_FILES["$uploadField"]["name"];		
		// dit optioneel maken?? (Alleen als er niet direct kan worden gelezen uit de tmp map??)
//		$tmp = Dir::uploaddata . basename($_FILES["$uploadField"]["tmp_name"]);
//		self::$uploadedFiles[] = $tmp;
//		move_uploaded_file($_FILES["$uploadField"]["tmp_name"], $tmp);
//		$file["tmp_name"] =  $tmp;
//		register_shutdown_function(array("RequestBasic", "uploadCleanUp"));
		
		self::$File = $_FILES["$uploadField"];
	}
	
	protected static function readJsonPostField($field)
	{
		$r = array();
		if(isset($_POST[$field]))
		{
			$json = $_POST[$field];
						
			if(!$json) return array();
			
			$r = json_decode($json, true);
			
			if(!is_array($r)) throw new Exception("Op \$_POST[$field] werd een JSON string verwacht, deze is onjuist geformatteerd of leeg: ". $json);
		
			//array_walk_recursive($r, array("StringFunctions", "utf8_encode"));
		}
		return $r;
	}
	
	// wordt automatisch aangeroepen -----------------------------
	
	public static function uploadCleanUp()
	{
		while($file = array_shift(self::$uploadedFiles)) @unlink($file);
	}
	

	// kan door public worden aageesproken -----------------------------
	
	public static function typeAjax()
	{
		return (bool) ( self::$Type & self::TYPE_AJAX );
	}
	
	public static function typeGoolge()
	{
		return (bool) ( self::$Type & self::TYPE_GOOGLE );
	}
	
	public static function give404($msg = "")
	{
		/*
		 * Deze functie niet gebruiker, liever direct zelf klasse aanroepen.
		 */
		new HTTP_Error404($msg);
	}
	
	public static function give403($msg = "")
	{
		/*
		 * Deze functie niet gebruiker, liever direct zelf klasse aanroepen.
		 */
		new HTTP_Error403($msg);
	}
	
	public static function redir($url, $httpCode = 302)
	{
		if($httpCode > 307 || $httpCode < 301 || $httpCode == 306) throw new Exception("Ongeldige redirect HTTP code gegeven: '$httpCode'");
		
		if(strpos($url, "http://") !== 0) // URI omzetten in URL
		{
			if(strpos($url, ".") === 0) throw  new Exception("Redir kan nog geen URI's verwerken die naar bovenliggende paden verwijzen (./, ../)");
			if(strpos($url, "/") !== 0) $url = self::$originalPath . "/" . $url;
		}
		header("Location: $url", true, $httpCode);
		exit;
	}
}