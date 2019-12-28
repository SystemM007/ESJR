<?php

class Module_Section_Constructor extends Module_Basic_Constructor
{	
	protected function getNewCoreName()
	{
		return "Nieuwe afdeling";
	}
	
	protected function getNewReadLevel()
	{
		return User::USERLEVEL_EASY;
	}
	
	protected function getNewWriteLevel()
	{
		return User::USERLEVEL_EASY;
	}
	
	protected function getNewChildCreateLevel()
	{
		return User::ACCESSLEVEL_SUPERUSER;
	}
	
	protected function getNewUrlName()
	{
		return 	"afdeling_". uniqid();
	}
	
	protected function getNewSiteModule()
	{
	}
	
	protected function getNewLinkAble()
	{
		return true;
	}
	
	protected function getNewRel()
	{
	}
	
	protected function getNewParamAllowed()
	{
		return false;
	}
	
	protected function getNewEnabled()
	{
		// voor terugwaardse compabiliteit.
		// alle uitbreidingen die offline moeten worden aangemaakt moeten dat dus alsnog expliciet aangeven.
		return (get_class($this) != __CLASS__);
	}
	
	protected function onCreate()
	{
		MySql::insert(array(
			"table" => "u_sections",
			"values" => array(
				"ID" => $this->Core->ID,
				"urlName" => $this->getNewUrlName(),
				"siteModule" => $this->getNewSiteModule(),
				"linkAble" => $this->getNewLinkAble(),
				"rel" => $this->getNewRel(),
				"paramAllowed" => $this->getNewParamAllowed(),
			)
		));
	}
	
	protected function onDelete()
	{
		MySql::delete(array(
			"table" => "u_sections",
			"where" => "ID = '" . $this->Core->ID . "'",
			"limit" => 1
		));
		
		parent::onDelete();
	}

}