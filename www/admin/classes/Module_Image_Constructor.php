<?php

class Module_Image_Constructor extends Module_Basic_Constructor
{	
	protected function getNewCoreName($Parent = NULL, $file = NULL)
	{
		if(!isset($file)) throw new Exception("Geen file gegeven bij constructie van Image");	
		
		$name = $file["name"];
		$name = basename($name, strrchr($name, "."));
		$name = str_replace(array("_"), " ", $name);
		$name = trim($name);
		$name = ucfirst(strtolower($name));
		
		return $name;
	}
	
	// Geen kinderen!
	protected function getNewChildrenAllowed()
	{
		return false;
	}
	
	protected function getNewReadLevel()
	{
		return User::USERLEVEL_EASY;
	}
	
	protected function getNewWriteLevel()
	{
		return User::USERLEVEL_EASY;
	}
	
	protected function onCreate($file = NULL)
	{
		if(!isset($file)) throw new Exception("Geen file gegeven bij constructie van Image");
	
		parent::onCreate();
		
		$Image = new ImageResizer($file["tmp_name"]);
		
		$name = $this->Core->ID . ".jpg";
		
		$this->writeImages($Image, $name);
		
		MySql::insert(array(
			"table" => "u_images",
			"values" => array(
				"ID" => $this->Core->ID,
			)
		));
	}
	
	protected function writeImages(ImageResizer $Image, $name)
	{
		$Image->storeResized(Dir::textimages . "image/$name", Settings::get("textImageMaxSize"), Settings::get("textImageQuality"));
		$Image->storeResized(Dir::textimages . "thumb/$name", 120, 70);
		$Image->storeResized(Dir::textimages . "full/$name");
	}
	
	protected function onDelete()
	{
		parent::onDelete();

		$name = $this->Core->ID . ".jpg";
	
		// let op: full wordt niet verwijderd!
		$this->removeImages($name);
		
		MySql::delete(array(
			"table" => "u_images",
			"where" => "`ID` = '" . $this->Core->ID . "'",
			"limit" => 1,
		));
	}
	
	protected function removeImages($name)
	{
		$this->saveUnlink(Dir::textimages . "image/$name");
		$this->saveUnlink(Dir::textimages . "thumb/$name");
	}
	
	protected function saveUnlink($file)
	{
		if(! file_exists($file) ) Response::msg("Kon niet verwijderen: '$file'");
		else unlink($file);
	}
	
}