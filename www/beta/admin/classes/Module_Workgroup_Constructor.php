<?php

class Module_Workgroup_Constructor extends Module_WebPage_Constructor
{	
	protected function getNewCoreName($Parent = NULL, $file = NULL)
	{
		return "Nieuwe werkgroep";
	}
	
	protected function getNewUrlName()
	{
		return 	"werkgroep_". uniqid();
	}
	
	protected function getNewSiteModule()
	{	
		return "Workgroup";
	}
	
	/*
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
	*/
	
	protected function getNewEnabled()
	{
		return false;
	}
	
	protected function getNewTitle()
	{
		return "Nieuwe werkgroep";
	}
	
	protected function getNewText()
	{
		return "<p>Nog geen tekst hierin.</p>";
	}
	
	protected function getNewChildrenAllowed()
	{
		return true;
	}
	
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
		MySql::insert(array(
			"table" => "w_workgroup",
			"values" => array(
				"ID" => $this->Core->ID
			)
		));
		
		parent::onCreate();
	}
	
	protected function onDelete()
	{
		MySql::delete(array(
			"table" => "w_workgroup",
			"where" => "`ID` = '" . $this->Core->ID . "'",
			"limit" => 1,
		));
		
		parent::onDelete();
	}
}