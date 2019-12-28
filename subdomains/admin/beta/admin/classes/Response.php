<?php

class Response
{

	protected static $response = array(
		// let op! Geen lege array voor HASHES, dit wordt een [], en geen {} in JSON
		"title" => "",
		"pageFields" => array(
			"title" => "",
			"buttons" => "",
			"pageNumbers" => "",
			"msg" => "",
			"tip" => "",
			"error" => ""
		),
		"template" => "",
		"templateFields" => NULL, // HASH 
		"editables" => array(),
		"editablesFeedback" => NULL, // HASH
		"childList" => NULL, // HASH
		"uploads" => array(),
		"evalJS" => "",
		"onSubmit" => "",
	);
	
	protected static $timeLog = array();
	
	private static $sendNoUploadResponse = false;
	private static $isUpload;
	
	public static $startTime;
	
	public static function sendNoUploadResponse($set = true)
	{
		// belangrijk! Als er voor het eerst een upload veld wordt geplaats mag er geen response worden gegeven!!
		self::$sendNoUploadResponse = $set;
	}

	public static function isUpload($set = true)
	{
			self::$isUpload = $set;
	}

	// ------------ finish ---------------------------
	
	public static function finish()
	{
		$h = request_headers();	
		
		//if(self::$isUpload)
		//{
		//	self::responseToUpload();
		//}
		if(! (self::$isUpload || $h["x-requested-with"] == "XMLHttpRequest") )
		{
			self::printR();
		}
		else
		{
			self::echoJson();
		}
	}
	
	protected static function printR()
	{
		$d = print_r(self::$response, true);
		//$d = json_encode(self::$response);
		
		//$d = htmlentities($d);
		//$d = str_replace(" ", "&nbsp;", $d);
		//$d = nl2br($d);
		
		echo $d;

	}
	
/*	protected static function responseToUpload()
	{
//		vds($_POST, true);
		
		$t = new Template(Dir::uw_admin_files . "uploadResponse.tpl.htm");
		$t->fill("action", $_POST["action"]);
		$t->fill("lifeId", $_POST["lifeId"]);
		$t->fill("query", $_POST["query"]);
		$t->fill("instanceId", $_POST["instanceId"]);
		
		if(!self::$sendNoUploadResponse) $t->fill("response", addslashes(json_encode(self::$response)));
		
		echo $t->content(); 
	}
*/
	
	protected static function echoJson()
	{
		$jsonStr = json_encode(self::$response);
		header("Content-Type:application/json;charset=utf-8");
		
		echo $jsonStr;
	}
	
	
	/* ------------------ Module Response ------------------------- */
	
	public static function msg($msg)
	{
		if(self::$response["pageFields"]["msg"])
		{
			$msg = " /// $msg";
		}
		
		self::$response["pageFields"]["msg"] .= (string) $msg;
	}
	
	public static function tip($tip, $before = false)
	{
		if($before)
		{
			self::$response["pageFields"]["tip"] = $tip . self::$response["pageFields"]["tip"];
		}
		else 
		{
			self::$response["pageFields"]["tip"] .= $tip;
		}
	}
	
	public static function error($error)
	{
		self::$response["pageFields"]["error"] = (string) $error;
	}
	
	// -------------------- template ---------------------
	
	public static function field($field, $html)
	{
		self::$response["templateFields"][$field] .= (string) $html;
	}
	
	// had liever andere oplossing gedaan, maar dit is wel lekker terugwaardsbruikbaar
	// wordt gebruikt door: Location
	public static function fieldErase($field)
	{
		self::$response["templateFields"][$field] = "";
	}
	
	// ----------------------- editables --------------------
	
	public static function editable($id, array $options)
	{
		if(!isset($options["editType"])) throw new Exception("Options voor editable '$id' zijn incorrect: geen editType gegeven");
		self::$response["editables"][] = compact("id", "options");
	}
	
	public static function childList($id, array $options)
	{
		if(!isset($id)) throw new Exception("Options voor childList zijn incorrect: geen id gegeven");
		if(!isset($options["lifeId"])) throw new Exception("Options voor childList '$id' zijn incorrect: geen lifeId gegeven");
				
		self::$response["childList"][$id] = $options;
	}
	
	public static function feedback($id, $type)
	{
		self::$response["editableFeedback"][$id] = $type;
	}
	
	// ----------------------- upload --------------------
	
	public static function upload($id, array $options)
	{
		self::$response["uploads"][] = compact("id", "options");
	}
	
	// ----------------------- js --------------------
	public static function evalJS($js)
	{
		self::$response["evalJS"] .= "$js;";
	}
	
	public static function onSubmit($js)
	{
		self::$response["onSubmit"] .= "$js;";
	}
	
	
	
	
	
	
	/* ---------------- Page Response --------------- */
	
	
	// ------------ page fields ---------------------


	public static function title($str)
	{
		self::$response["pageFields"]["title"] .= (string) $str;
	}
	
	public static function buttons($str)
	{
		self::$response["pageFields"]["buttons"] .= (string) $str;
	}
	
	public static function pagenumbers($str)
	{
		self::$response["pageFields"]["pageNumbers"] = (string) $str;
	}

	
	// -------------------- template ---------------------
	
	public static function template($str)
	{
		self::$response["template"] .= (string) $str;
	}
}