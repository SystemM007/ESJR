<?php

abstract class Module_Universal_Children extends Module_Basic_Children
{
	protected function makeCreateInput()
	{
		$modules = MySql::select(array(
			"select" => array("u_modules.module", "u_cores.name"),
			"from" => "u_modules",
			"join" => array("table" => "u_cores", "using" => "ID"),
			"where" => "u_modules.universal != '0' AND u_modules.constructLevel >= '". User::getLevel() . "'"
		));
		
		return (string) new Fragment_Select_Create($modules, "module", "name", $this->Core, array(), "Nieuwe afdeling: ");
	}
	
	public function childTypeIsAllowed($module)
	{
		$universal = MySql::selectValue(array(
			"select" => "universal",
			"from" => "u_modules",
			"where" => "`module` = '$module'",
		));
		return (bool) $universal;
	}
}