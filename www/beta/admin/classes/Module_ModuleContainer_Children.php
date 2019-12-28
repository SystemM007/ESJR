<?php

class Module_ModuleContainer_Children extends Module_Basic_Children
{

	protected function listOrder()
	{
		return "u_cores.name";
	}

	protected function makeCreateInput()
	{
		return (string) new Fragment_Button_Create("Nieuwe module", $this->getLifeID(), "Module");
	}
	
	
	public function childTypeIsAllowed($module)
	{
		return ($module == "Module") ;
	}
}