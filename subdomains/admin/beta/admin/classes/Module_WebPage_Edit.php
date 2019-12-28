<?php

class Module_WebPage_Edit extends Module_Section_Edit
{
	public function makeEditables()
	{	
		$editables = "";
		
		// naam en title en url
		$editables .= $this->coreName();
		$editables .= $this->webPageTitle();
		
		if(User::levelAllowed($this->urlEditLevel))
			$editables .= $this->sectionUrlName();
			
		// modulen
		if(User::levelAllowed(User::ACCESSLEVEL_HIGHEST)) // sowieso niet te bedoeling!
			$editables .= $this->coreModule();
		if(User::levelAllowed(User::ACCESSLEVEL_SUPERUSER))
			$editables .= $this->sectionSiteModule();
					
		// rechten	
		if(User::levelAllowed(User::ACCESSLEVEL_NORMAL)) // niet voor easy
		{	
			$editables .= $this->coreReadLevel();
			$editables .= $this->coreWriteLevel();
			$editables .= $this->coreChildCreateLevel();
		}
		if(User::levelAllowed(User::ACCESSLEVEL_SUPERUSER))
		{
			$editables .= $this->coreChildrenAllowed();
			$editables .= $this->sectionLinkable();
		}
		
		$editables.= $this->coreEnabled();		
		// tekst
		$editables .= $this->webPageText();
		
		return $editables;
	}
	
	protected function webPageTitle($desc = "Pagina Titel")
	{
		$Edit = new Editable_Text($desc, array("u_webpages", "ID", $this->getID(), "title"));
		$Edit->checkMaxLength(100);
		$Edit->checkNonEmpty();
		if($this->firstEdit) $Edit->optionGiveFocus();
		$this->addEditable($Edit);
		return $Edit;
	}
		
	protected function webPageText($desc = "Tekst")
	{
		$Edit = new Editable_Tiny($desc, array("u_webpages", "ID", $this->getID(), "text"));
		$this->addEditable($Edit);
		return $Edit;
	}
	
	private function convertAllowed()
	{
		return (get_class($this) == "Module_WebPage_Edit" && User::levelAllowed(User::ACCESSLEVEL_SUPERUSER));
	}
	
	public function getButtons()
	{
		$buttons = parent::getButtons();
		if($this->convertAllowed())
		{
			$buttons .= new Fragment_Button_Action("Convert &gt; Section", $this->getLifeId(), "convertToSection", array(), array(), "Dit verwijderd de tekst van de webpagina! Pas handmatig de site module aan!");
		}
		return $buttons;
	}
	
	public function isRequestable($function)
	{
		if($this->convertAllowed() && $function == "convertToSection")
		{
			return true;
		}
		else
		{
			return parent::isRequestable($function);
		}
	}
	
	public function convertToSection()
	{
		$this->Core->Constructor->deleteWebPage();
		
		MySql::update(array(
			"table" => "u_cores",
			"values" => array(
				"module" => "Section",
			),
			"where" => "ID = '". $this->Core->ID ."'",
		));
		
		Response::evalJs(new Fragment_JS_History(0));
		/*
		 * Ik doe hier verder niets aan de siteModule, mag je lekker handmatig doen
		 */
	}
}