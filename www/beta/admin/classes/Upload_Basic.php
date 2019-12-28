<?php

class Upload_Basic
{
	public function isRequestable($function)
	{
		return (in_array($function, array("upload")));
	}

	protected $ServerAction;
	protected $id;
	protected $text;
	protected $fileExtentions = array();
	protected $mimeTypes = array();
	protected $options = array(
		"file_types" => NULL, // default *.*
		"file_types_description" => "Upload bestanden",
		"file_size_limit" => "",
		"multiple_files" => false, // GEEEEEEEEN setting van swfu, maar van mijn meuje script
		"lifeId" => 0, // dit is het life id van de ServerAction
		"Cookies" => array(),
	);
	protected $callback;
	
	final public function __construct($text, $callback)
	{
		$this->text = $text;
		$this->setCallback($callback);
		
		
		$this->setFileSizeLimit(maxUploadBytes());
		
		$this->ServerAction = new ServerAction(array($this, "upload")); 
		$this->id = "upload_" . $this->ServerAction->getLifeId();
		$this->options["lifeId"] = $this->ServerAction->getLifeId();
		$this->options["Cookies"] = json_encode($_COOKIE);
		$this->options["buttontext"] = $text;
		
		$this->onConstruct();
	}
	
	public function setFileTypesDescription($desc)
	{
		$this->options["file_types_description"] = $desc;
	}
	
	public function setFileSizeLimit($limit)
	{
		$this->options["file_size_limit"] = $limit;
	}
	
	public function setMultipleFiles($set = true)
	{
		$this->options["multiple_files"] = $set;
	}
	
	public function addFileExtentions($extentions)
	{
		if(!is_array($extentions)) $extentions = array($extentions);
		
		$this->fileExtentions = array_merge($this->fileExtentions, $extentions);
		
		if(!isset($this->options["file_types"])) $this->options["file_types"] = "";
		foreach($extentions as $ext) $this->options["file_types"] .= "*.$ext;";
	}
	
	public function addMimeTypes($mimeTypes)
	{
		if(!is_array($mimeTypes)) $mimeTypes = array($mimeTypes);
		
		$this->mimeTypes = array_merge($this->mimeTypes, $mimeTypes);
	}
	
	public function setCallback($callback)
	{
		$this->callback = $callback;
	}
	
	/*
	* Deze onderstaande drie functies dienen maar ��n keer te worden aangeroepen
	* Omdat ze de settings naar response verzenden
	*/
	public function getUploadButton()
	{	
		$this->toResponse();
		
		$html = "";
		//$html = "<button type='button' class='upload' onclick='$link'>$this->text</button>";
		$html .= "<span id='$this->id'></span>";
		return $html;
	}
	
	public function getUploadLink()
	{
		throw new Exception("Deze functie werkt op het moment niet meer...");
	}
	
	final public function __toString()
	{
		return $this->getUploadButton();
	}
	
	final public function upload()
	{
		if(!Request::$File) Response::msg("Geen bestand ontvangen!");
		
		$File = Request::$File;
		
		try
		{
			$this->checkExtention($File);
			$this->checkMimeType($File);
			$this->checkUpload($File);
		}
		catch(Exception $E)
		{
			Response::msg("Upload incorrect: " . $E->getMessage() );
			return;
		}
		
		call_user_func($this->callback, $File);
	}
	
	// mag worden override
	protected function onConstruct(){}
	protected function checkUpload($File){}
	
	final private function toResponse()
	{
		Response::upload($this->id, $this->options);
	}
	
	private function checkExtention($File)
	{
		if(!$this->fileExtentions) return;
		$extention = strtolower(substr(strrchr($File["name"], "."), 1));
		if(!in_array($extention, $this->fileExtentions)) throw new Exception("Onjuiste extentie: $extention, toegestaan: " . implode(", ", $this->fileExtentions));
	}
	
	private function checkMimeType($File)
	{
		if(!$this->mimeTypes) return;
		if(class_exists("finfo"))
		{
			$Finfo = new finfo(FILEINFO_MIME);
			$type = $Finfo->file($File["tmp_name"]);
		}
		elseif(function_exists("mime_content_type"))
		{
			$type = mime_content_type($File["tmp_name"]);
		}
		else
		{
			return; // tja...!!
		}
		if(!in_array($type, $this->mimeTypes)) throw new Exception("Onjuiste bestandsinhoud");
	}
}
		
		
		
		
		