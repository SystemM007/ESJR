<?php

class Module_WorkgroupContainer_Children extends Module_Basic_Children
{
	protected $sortable = true;
	protected $displayCoreEnabled = true;
	
	protected function makeCreateInput()
	{
		return (string) new Fragment_Button_Create("Nieuwe werkgroep", $this->Core, "Workgroup");
	}
	
	public function childTypeIsAllowed($module)
	{
		return ($module == "Workgroup") ;
	}
}