<?php
class Module_Section_Edit extends Module_Basic_Edit
{
	protected $urlEditLevel = User::ACCESSLEVEL_SUPERUSER;

	public function makeEditables()
	{	
		$editables = "";
		
		// naam en title en url
		$editables .= $this->coreName();
		
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
			$editables .= $this->sectionParamAllowed();
			$editables .= $this->coreChildrenAllowed();
			$editables .= $this->sectionLinkable();
		}
		
		$editables.= $this->coreEnabled();
		
		return $editables;
	}
	
	protected function sectionUrlName($desc = "URL")
	{
		$Edit = new Editable_UriPart($desc, array("u_sections", "ID", $this->getID(), "urlName"));
		$Edit->checkMaxLength(100);
		$Edit->checkNonEmpty();
		$this->addEditable($Edit);
		return $Edit;
	}
	
	protected function sectionSiteModule($desc = "Site module")
	{
		$Edit = new Editable_ClassName($desc, array("u_sections", "ID", $this->getID(), "siteModule"));
		$Edit->setUnderscoreAllowed();
		$Edit->checkNonEmpty();
		$this->addEditable($Edit);
		return $Edit;
	}
	
	protected function sectionParamAllowed($desc = "Parameters toegestaan")
	{
		$Edit = new Editable_Bool($desc, array("u_sections", "ID", $this->getID(), "paramAllowed"));
		$this->addEditable($Edit);
		return $Edit;
	}
	
	protected function sectionLinkable($desc = "Toevoegen in linklijst")
	{
		$Edit = new Editable_Bool($desc, array("u_sections", "ID", $this->getID(), "linkable"));
		$this->addEditable($Edit);
		return $Edit;
	}
	
	private function convertAllowed()
	{
		return (get_class($this) == "Module_Section_Edit" && User::levelAllowed(User::ACCESSLEVEL_SUPERUSER));
	}
	
	public function getButtons()
	{
		$buttons = parent::getButtons();
		
		if($this->convertAllowed())
		{
			$buttons .= new Fragment_Button_Action("Convert &gt; WebPage", $this->getLifeId(), "convertToWebpage", array(), array(), "Dit voegt de tekst van een webpagina toe! Pas handmatig de site module aan!");
		}

		return $buttons;
	}
	
	public function isRequestable($function)
	{
		if($this->convertAllowed() && $function == "convertToWebpage")
		{
			return true;
		}
		else
		{
			return parent::isRequestable($function);
		}
	}
	
	public function convertToWebPage()
	{
		$Constructor = new Module_WebPage_Constructor($this->Core);
		$Constructor->addWebPage();
		
		MySql::update(array(
			"table" => "u_cores",
			"values" => array(
				"module" => "WebPage",
			),
			"where" => "ID = '". $this->Core->ID ."'",
		));
		
		Response::evalJs(new Fragment_JS_History(0));
		/*
		 * Ik doe hier verder niets aan de siteModule, mag je lekker handmatig doen
		 */
	}
}