<?php

abstract class Module_Universal_Edit extends Module_Basic_Edit
{
	protected function makeEditables()
	{	
		$editables = "";
	
		if(User::levelAllowed(User::ACCESSLEVEL_NORMAL)) // niet voor easy
			$editables .= $this->coreName();
		
		if(User::levelAllowed(User::ACCESSLEVEL_HIGHEST)) // sowieso niet te bedoeling!
			$editables .= $this->coreModule();
			
		if(User::levelAllowed(User::ACCESSLEVEL_NORMAL)) // niet voor easy
			$editables .= $this->coreReadLevel();
		
		if(User::levelAllowed(User::ACCESSLEVEL_NORMAL)) // niet voor easy
			$editables .= $this->coreWriteLevel();
		
		if(User::levelAllowed(User::ACCESSLEVEL_NORMAL)) // niet voor easy
			$editables .= $this->coreChildCreateLevel();
		
		return $editables;
	}
}