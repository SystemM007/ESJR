<?php

class Module_Setting_Constructor extends Module_Basic_Constructor
{	
	protected function getNewCoreName()
	{
		return "Nieuwe Setting";
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
			"table" => "u_settings",
			"values" => array(
				"ID" => $this->Core->ID,
				"name" => "__leeg__" . uniqid(),
				"value" => "",
			),
		));
		
	}
	
	protected function onDelete()
	{
		parent::onDelete();
		
		MySql::delete(array(
			"table" => "u_settings",
			"where" => "`ID` = '". $this->Core->ID ."'",
			"limit" => "1",
		));
	}
}