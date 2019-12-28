<?php

class Module_User_Constructor extends Module_Basic_Constructor
{	
	protected function getNewCoreName()
	{
		return "Nieuwe Gebruiker";
	}
	
	// Geen kinderen!
	protected function getNewChildrenAllowed()
	{
		return false;
	}
	
	/*
		Schrijfrechten van een gebruiker veranderen automatisch met zijn userLevel
		
		Readlevel staat altijd open, zodat altijd alle gebruikers zichtbaar zijn
	*/
	
	protected function getNewReadLevel()
	{
		return User::USERLEVEL_EASY;
	}
	
	protected function getNewWriteLevel()
	{
		return User::USERLEVEL_EASY;
	}
	
	protected function onCreate()
	{
		parent::onCreate();
		
		MySql::insert(array(
			"table" => "u_adminusers",
			"values" => array(
				"ID" => $this->Core->ID,
				"userName" => "__leeg__" . uniqid(),
				"password" => "",
				"userLevel" => User::USERLEVEL_EASY,
			),
		));
		
	}
	
	protected function onDelete()
	{
		parent::onDelete();
		
		MySql::delete(array(
			"table" => "u_adminusers",
			"where" => "`ID` = '". $this->Core->ID ."'",
			"limit" => "1",
		));
	}
}