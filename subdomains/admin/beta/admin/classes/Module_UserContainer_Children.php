<?php

class Module_UserContainer_Children extends Module_Basic_Children
{
	protected function makeCreateInput()
	{
		return (string) new Fragment_Button_Create("Nieuwe gebruiker", $this->getLifeId(), "User");
	}
	
	public function childTypeIsAllowed($module)
	{
		return ($module == "User") ;
	}
}