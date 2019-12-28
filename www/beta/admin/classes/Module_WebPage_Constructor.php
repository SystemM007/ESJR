<?php

class Module_WebPage_Constructor extends Module_Section_Constructor
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
		return false;
	}
	
	protected function getNewUrlName()
	{
		return 	"page_". uniqid();
	}
	
	protected function getNewSiteModule()
	{	
		return "WebPage";
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
		// voor terugwaardse compabiliteit.
		// alle uitbreidingen die offline moeten worden aangemaakt moeten dat dus alsnog expliciet aangeven.
		return (get_class($this) != __CLASS__);
	}
	
	protected function getNewTitle()
	{
		return "Nieuwe afdeling";
	}
	
	protected function getNewText()
	{
		return "<h1>Nieuwe afdeling</h1><p>Met nog geen tekst daarin</p>";
	}
	
	protected function onCreate()
	{
		$this->addWebPage();

		parent::onCreate();
	}
	
	protected function onDelete()
	{
		$this->deleteWebPage();
		parent::onDelete();
	}
	
	/*
	 * Kan door edit worden aangeroepen door Edit om te converteren
	 */
	
	public function addWebPage()
	{
		MySql::insert(array(
			"table" => "u_webpages",
			"values" => array(
				"ID" => $this->Core->ID,
				"title" => $this->getNewTitle(),
				"text" => $this->getNewText(),
			)
		));
	}
	
	public function deleteWebPage()
	{
		MySql::delete(array(
			"table" => "u_webpages",
			"where" => "ID = '" . $this->Core->ID . "'",
			"limit" => 1
		));
	}
}