<?php

class StringFunctions {

	public static $optionsTrunicateLen = 25;
	public static $optionsTrunicateSuffix = "...";
	public static $optionsDateFormat = "j-m-Y";
	
	public static $optionUrlNameLength = 20;
	
	
	public static function makeUrlName(&$string)
	{
		$string = strtolower($string);
		$string = html_entity_decode($string);
		$string = str_replace(array(" ", "\n", "\t"), "-", $string);
		
		$string = rawurlencode($string);
		
		return $string;	
	}
	
	
	public static function nbspForEmpty(&$string){

		$string = (string) $string;
		
		$string = trim($string);
		
		if(strlen($string) == 0){
			$string = "&nbsp;";
		}
		return $string;
	}
	
	
	public static function stripTags(&$string, $allowable_tags = NULL)
	{
		// handige newlines invoegen
		
		// self closing tags
		$selfClosing = array("br", "hr"); // meuje regex maken zodat er ook nog attributen in de tag kunnen zitten? e.g. <hr class="dik" />
		foreach($selfClosing as $tag) 
		{
			if(strpos($allowable_tags, "<$tag>") === false) 
			{
				$string = str_ireplace(array("<$tag>", "<$tag />"), "<$tag />\n", $string);
			}
		}
		
		// elementen op block niveau
		$blockTags = array("h1", "h2", "h3", "h3", "h4", "p", "div");
		foreach($blockTags as $tag) 
		{
			if(strpos($allowable_tags, "<$tag>") === false) 
			{
				//$string = str_ireplace("<$tag>", "<$tag>\n", $string);
			}
		}
		
		// extra spaties spaties invoegen, zodat <p>Zin 1.</p><p>Zin2</p> netjes van een tussen spatie wordt voorzien.
		$string = str_replace("<", " <", $string);	
		while(strpos($string, "  ") !== false) $string = str_replace("  ", " ", $string); // alle dubbele spaties verwijderen
		while(strpos($string, "\n ") !== false) $string = str_replace("\n ", "\n", $string); // alle doelloze spaties verwijderen.
		while(strpos($string, " \n") !== false) $string = str_replace(" \n", "\n", $string); // alle doelloze spaties verwijderen.
		$string = trim($string);
		
		// en jawel, ook nog ff die tags eruit
		$string = strip_tags($string);
		
		return $string;
	}
	
	public static function trunicate(&$str, $len = false, $suffix = false){
	
		$len = $len ? $len : self::$optionsTrunicateLen;
		$suffix = $suffix ? $suffix : self::$optionsTrunicateSuffix;

		$str = self::stripTags($str);
		
		if($len > strlen($str)) return $str;
		
		$pos = strpos($str, " ", $len);
			
		if($pos !== false){
		
			//if there is a space after position $pos: cut the rest away and add "..."
			$str = substr($str, 0, $pos);
			$str .= $suffix ;
		}
		
		return $str;

	}
	
	public static function nl2br(&$string){
	
		// alias
		$string = nl2br($string);
		
		return $string;
	}
	
	public static function date(&$string, $format = false){
	
		// alias met reference en omgekeerde argumenten
		$format = $format ? $format : self::$optionsDateFormat;
		
		/*
		 * unix timestamp
		 */
		if(is_numeric($string))
		{
			$timestamp = (int) $string;
		}
		else
		{
			/*
			 * mysql date
			 */
			// timestamp: date eraf halen
			if(strpos($string, " ")) $string = substr($string, 0, strpos($string, " "));
			// date lezen
			list($y, $m, $d) = explode("-", $string);
			// is het wel een date?
			if(checkdate((int)$m, (int)$d, (int)$y))
			{
				$timestamp = mktime(0, 0, 0, $m, $d, $y);
			}
			else
			{
				throw new Exception("Ongeldige date formulering: '$string'");
			}
		}
		
		$string = date($format, $timestamp);
		
		return $string;
	}
	
	public static function sprintf($string, $format)
	{
		// karige boel, maar argumenten moesten nu eenmaal omgekeerd	
		return sprintf($format, $string);
	}
	
	public static function utf8_encode(&$string){
		// alias met reference
		if(is_string($string)){
			$string = utf8_encode($string);
		}
		
		return $string;
	}
	
	public static function wrap(&$string, $prefix = "", $suffix = "")
	{
		$string = $prefix . $string . $suffix;
		
		return $string;
	}

	public static function replaceMailto(&$string){
	
	
		$pattern = "/href=(\"|')(mailto:.*?)(\"|')(.*?)>/i";
		
		preg_match_all($pattern, $string, $r);

		$geheel = $r[0];
		$quotes = $r[1];
		$email_adressen = $r[2];
		
		for($i = 0; $i < count($geheel); $i++){
			
			$email = $email_adressen[$i];
			$quoteStyle = $quotes[$i];
			
			$email_s = str_split($email, 2);
			
			if($quoteStyle == "\""){
				$email_f = implode("'+'", $email_s);
				$href = "Javascript:location.href='$email_f'";
			}
			else{
				$email_f = implode("\"+\"", $email_s);
				$href = "Javascript:location.href=\"$email_f\"";
			}
		
			$string =  str_replace($email, $href, $string);
		}
		
		return $string;
	}
	
}