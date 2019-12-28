<?php

class Module_Module_Edit extends Module_Basic_Edit
{
	protected function makeEditables()
	{	
		$editable .= "";
		
		$editables .= $this->coreName("Module naam");
			
		if(User::levelAllowed(User::ACCESSLEVEL_NORMAL)) // niet voor easy
			$editables .= $this->coreReadLevel();
		
		if(User::levelAllowed(User::ACCESSLEVEL_NORMAL)) // niet voor easy
			$editables .= $this->coreWriteLevel();
		
		$editables .= $this->module();
		$editables .= $this->constructLevel();
		$editables .= $this->universal();
		$editables .= $this->isSection();
		
		return $editables;
	}
	
	protected function module()
	{
		$Edit = new Editable_ClassName("Module", array("u_modules", "ID", $this->getID(), "module"));
		$Edit->setUnderscoreAllowed();
		$Edit->checkNonEmpty();
		$Edit->checkMustBeUnique();
		$this->addEditable($Edit);
		return $Edit;
	}
	
	protected function constructLevel()
	{
		$Edit = new Editable_AccessSelect("Construct Level", array("u_modules", "ID", $this->getID(), "constructLevel"));
		$this->addEditable($Edit);
		return $Edit;
	}
	
	protected function universal()
	{
		$Edit = new Editable_Bool("Universal", array("u_modules", "ID", $this->getID(), "universal"));
		$this->addEditable($Edit);
		return $Edit;
	}
	
	protected function isSection()
	{
		$Edit = new Editable_Bool("Is Section", array("u_modules", "ID", $this->getID(), "isSection"));
		$this->addEditable($Edit);
		return $Edit;
	}
}