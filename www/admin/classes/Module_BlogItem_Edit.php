<?php

class Module_BlogItem_Edit extends Module_WebPage_Edit
{
	public function makeEditables()
	{	
		$editables = "";
	
		$editables .= $this->coreName("Column titel (kort)");
		$editables .= $this->webPageTitle("Column titel (uitgebreid)");
		$editables .= $this->sectionUrlName();
		
		$editables .= $this->blogDate();
		$editables .= $this->blogAuthor();
					
		if(User::levelAllowed(User::ACCESSLEVEL_HIGHEST)) // sowieso niet te bedoeling!
		{
			$editables .= $this->coreModule();
			$editables .= $this->coreReadLevel();
			$editables .= $this->coreWriteLevel();
			$editables .= $this->coreChildCreateLevel();
		}
		$editables.= $this->coreEnabled();	
		$editables .= $this->blogIntroduction();
		$editables .= $this->webPageText();
		
		return $editables;
	}
	
	protected function blogDate()
	{
		$Edit = new Editable_MDate("Datum", array("w_blog", "ID", $this->getID(), "date"));
		$this->addEditable($Edit);
		return $Edit;
	}
	
	protected function blogAuthor()
	{
		$Edit = new Editable_Text("Auteur", array("w_blog", "ID", $this->getID(), "author"));
		$Edit->checkMaxLength(100);
		$this->addEditable($Edit);
		return $Edit;
	}
	
	protected function blogIntroduction()
	{
		$Edit = new Editable_Tiny("Inleiding", array("w_blog", "ID", $this->getID(), "introduction"));
		$this->addEditable($Edit);
		return $Edit;
	}
}