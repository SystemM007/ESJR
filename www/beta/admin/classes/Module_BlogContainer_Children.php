<?php

class Module_BlogContainer_Children extends Module_Basic_Children
{
	protected $sortable = true;
	protected $displayCoreEnabled = true;
	
	protected function makeCreateInput()
	{
		return (string) new Fragment_Button_Create("Nieuwe column", $this->Core, "BlogItem");
	}
	
	public function childTypeIsAllowed($module)
	{
		return ($module == "BlogItem") ;
	}
}