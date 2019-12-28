<?php

class Module_Setting_Edit extends Module_Basic_Edit
{
	protected function makeEditables()
	{	
		$editables = "";
	
		$editables .= $this->coreName("Naam setting");
		
		if(User::levelAllowed(User::ACCESSLEVEL_NORMAL)) // niet voor easy
			$editables .= $this->coreReadLevel();
		
		if(User::levelAllowed(User::ACCESSLEVEL_NORMAL)) // niet voor easy
			$editables .= $this->coreWriteLevel();
		
		$editables .= $this->settingName();
		$editables .= $this->settingValue();
		
		return $editables;
	}
	
	protected function settingName()
	{
		$Edit = new Editable_Text("Setting Key", array("u_settings", "ID", $this->Core->ID, "name"));
		$Edit->checkNonEmpty();
		$Edit->checkMustBeUnique();
		$this->addEditable($Edit);
		return $Edit;
	}
	
	protected function settingValue()
	{
		$Edit = new Editable_Text("Setting Waarde", array("u_settings", "ID", $this->Core->ID, "value"));
		$Edit->checkNonEmpty();
		$this->addEditable($Edit);
		
		return $Edit;
	}
}