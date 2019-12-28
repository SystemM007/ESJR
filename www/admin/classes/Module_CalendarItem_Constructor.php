<?php

class Module_CalendarItem_Constructor extends Module_Basic_Constructor
{	
	protected function getNewCoreName($Parent = NULL, $file = NULL)
	{
		return "Nieuw agendapunt";
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
	
	protected function getNewEnabled()
	{
		return false;
	}
	
	protected function onCreate()
	{
		MySql::insert(array(
			"table" => "w_calendar",
			"values" => array(
				"ID" => $this->Core->ID,
				"date" => date("y-m-d" ),
			)
		));
		
		parent::onCreate();
	}
	
	protected function onDelete()
	{
		MySql::delete(array(
			"table" => "w_calendar",
			"where" => "`ID` = '" . $this->Core->ID . "'",
			"limit" => 1,
		));
		
		parent::onDelete();
	}
}