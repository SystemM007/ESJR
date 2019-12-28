<?php

abstract class Function_Transfer_Images extends Function_Abstract
{
	protected $accessLevel = User::ACCESSLEVEL_HIGHEST;
	

	protected $newIDs = array();
	
	
	final protected function startFunction()
	{
		$Images = $this->getImages();
		
		$this->transferImages($Images);

		$this->replaceImages();
	}
	
	protected function transferImages(Dataset $Images)
	{
		/*
		 * $Images: (oldID, name)
		 */
		
		$ImageContainer = new Core(Settings::get("imageContainerID"));
		
		foreach($Images as $image)
		{	
			$Constructor = new Module_Image_Constructor();
			$ImageCore = $Constructor->create($ImageContainer, array(
				"name" => $image["name"], 
				"tmp_name" => $this->getOldPath($image["oldId"]),
			));
			
			echo $image["name"] . "<br />\n";
			
			$this->newIDs[$image["oldId"]] = $ImageCore->ID;
		}
	}
	
	abstract protected function getImages();
	
	abstract protected function getOldPath($oldId);
	
	abstract protected function searchReplaceArray();
		
	protected function replaceImages()
	{
		list($search, $replace) = $this->searchReplaceArray();
		
		foreach(MySql::select("SELECT ID, text FROM u_webpages") as $webpage)
		{
			$text = str_replace($search, $replace, $webpage["text"]);
			MySql::update(array("table"=>"u_webpages", "values" => compact("text"), "where" => "ID = '{$webpage["ID"]}'"));
		}
	}
	
}	