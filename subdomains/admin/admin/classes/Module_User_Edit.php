<?php

class Module_User_Edit extends Module_Basic_Edit
{
	public function makeEditables()
	{	
		$editables .= $this->coreName("Naam van de gebruiker");
		
		$editables .= $this->userName();
		$editables .= $this->password();
		$editables .= $this->userLevel();
		
		return $editables;
	}
	
	protected function userName()
	{
		$Edit = new Editable_ClassName("Gebruikersnaam", array("u_adminusers", "ID", $this->getID(), "userName"));
		$Edit->checkNonEmpty();
		$Edit->checkMustBeUnique();
		$this->addEditable($Edit);
		
		if(User::getID() == $this->getID()) // gebruiker bewerkt zichtzelf
		{
			$Edit->addCallBack(array($this, "onUsernameSave"));
		}
		
		return $Edit;
	}
	
	protected function password()
	{
		$Edit = new Editable_Passwd2Hash("Wachtwoord", array("u_adminusers", "ID", $this->getID(), "password"));
		$Edit->checkNonEmpty();
		$Edit->checkMustBeUnique();
		$this->addEditable($Edit);
		
		if(User::getID() == $this->getID()) // gebruiker bewerkt zichtzelf
		{
			$Edit->addCallBack(array($this, "onPasswordSave"));
		}
			
		return $Edit;
	}
	
	protected function userLevel()
	{
		if(User::getID() == $this->getID()) // gebruiker bewerkt zichtzelf
		{
			// kan eigen level niet veranderen.
			// downgraden geeft uiteraard problemen
			// upgraden mag nooit
			return ""; 
		}
				
		$Edit = new Editable_Select("Gebruikers Type", array("u_adminusers", "ID", $this->getID(), "userLevel"));
		$Edit->optionTable("u_adminUserTypes", "userLevel", "name", "u_adminUserTypes.userLevel >= '". User::getLevel() ."'");
		$this->addEditable($Edit);
		
		$Edit->addCallBack(array($this, "onLevelChange"));
		
		return $Edit;
	}
	
	public function onLevelChange($data)
	{
		$level = $data["value"]; // samen met user level ook write leven van Core veranderen!
		
		MySql::update(array(
			"table" => "u_cores",
			"values" => array(
				"writeLevel" => $level,
			),
			"where" => "`ID` = '" . $this->Core->ID . "'",
		));
	}
	
	public function onUsernameSave($data)
	{
		$username = $data["input"];
		
		User::$userName = $username;
		User::writeCookie(User::getUserName(), $_COOKIE["password"]);
	}
	
	public function onPasswordSave($data)
	{
		// als men het eigen password bewerkt moet
		// deze opnieuw worden weggeschreven
		$password = $data["input"];
		User::writeCookie(User::getUserName(), $password);
	}
}