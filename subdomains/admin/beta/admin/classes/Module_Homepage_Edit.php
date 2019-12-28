<?php

class Module_Homepage_Edit extends Module_WebPage_Edit
{
	public function makeEditables()
	{	
		$editables = "";
		
		// naam en title en url
		if(User::levelAllowed(User::ACCESSLEVEL_SUPERUSER)) // niet voor easy
			$editables .= $this->coreName();
		
		$editables .= $this->webPageTitle();
		
		//if(User::levelAllowed(User::ACCESSLEVEL_SUPERUSER))
		//	$editables .= $this->sectionUrlName();
			
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
			$editables .= $this->sectionLinkable();
		}
		
		// tekst
		$editables .= $this->webPageText();
		
		return $editables;
	}
}