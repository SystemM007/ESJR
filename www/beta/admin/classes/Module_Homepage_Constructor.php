<?php

class Module_Homepage_Constructor extends Module_WebPage_Constructor
{	
	protected function getNewReadLevel()
	{
		return User::USERLEVEL_EASY;
	}
	
	protected function getNewWriteLevel()
	{
		return User::USERLEVEL_EASY;
	}
	
	protected function getNewChildrenAllowed()
	{
		return true;
	}
	
	protected function getNewUrlName()
	{
		return 	"";
	}
	
	protected function getNewSiteModule()
	{	
		return "Section";
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
	
	protected function getNewTitle()
	{
		return "Homepage";
	}
	
	protected function getNewText()
	{
		return "<h1>Home</h1><p>Welkom</p>";
	}
}