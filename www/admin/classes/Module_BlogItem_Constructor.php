<?php

class Module_BlogItem_Constructor extends Module_WebPage_Constructor
{	
	protected function getNewCoreName($Parent = NULL, $file = NULL)
	{
		return "Nieuwe column";
	}
	
	protected function getNewUrlName()
	{
		return 	"column_". uniqid();
	}
	
	protected function getNewSiteModule()
	{	
		return "BlogItem";
	}
	
	protected function getNewChildrenAllowed()
	{
		return true;
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
		return "Nieuwe column";
	}
	
	protected function getNewText()
	{
		return "<p>Met nog geen tekst daarin</p>";
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
			"table" => "w_blog",
			"values" => array(
				"ID" => $this->Core->ID
			)
		));
		
		parent::onCreate();
	}
	
	protected function onDelete()
	{
		MySql::delete(array(
			"table" => "w_blog",
			"where" => "`ID` = '" . $this->Core->ID . "'",
			"limit" => 1,
		));
		
		parent::onDelete();
	}
}