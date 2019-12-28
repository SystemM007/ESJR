<?php

class Module_Workgroup_Edit extends Module_WebPage_Edit
{
	public function makeEditables()
	{	
		$editables = "";
	
		$editables .= $this->coreName("Werkgroepnaam");
		$editables .= $this->webPageTitle("Werkgroepnaam (uitgebreid)");
		$editables .= $this->sectionUrlName();
		
		
		if(User::levelAllowed(User::ACCESSLEVEL_HIGHEST)) // sowieso niet te bedoeling!
		{
			$editables .= $this->coreModule();
			$editables .= $this->coreReadLevel();
			$editables .= $this->coreWriteLevel();
			$editables .= $this->coreChildCreateLevel();
		}
		
		$editables .= $this->workgroupDescription();
		$editables .= $this->webPageText();
		
		$editables.= $this->coreEnabled();	
		
		return $editables;
	}
	
	protected function workgroupDescription()
	{
		$Edit = new Editable_Tiny("Korte beschrijving", array("w_workgroup", "ID", $this->getID(), "description"));
		$this->addEditable($Edit);
		return $Edit;
	}
}