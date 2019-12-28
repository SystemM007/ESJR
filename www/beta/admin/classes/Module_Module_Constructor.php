<?php

class Module_Module_Constructor extends Module_Basic_Constructor
{	
	protected function getNewCoreName()
	{
		return "Nieuwe module";
	}
	
	// Geen kinderen!
	protected function getNewChildrenAllowed()
	{
		return false;
	}
	
	protected function onCreate()
	{
		parent::onCreate();
		
		MySql::insert(array(
			"table" => "u_modules",
			"values" => array(
				"ID" => $this->Core->ID,
				"module" => "__leeg__" . uniqid(),
				"constructLevel" => 0,
				"universal" => 0,
				"isSection" => 0,
			),
		));
		
	}
	
	protected function onDelete()
	{
		parent::onDelete();
		
		MySql::delete(array(
			"table" => "u_modules",
			"where" => "`ID` = '". $this->Core->ID ."'",
			"limit" => "1",
		));
	}
}