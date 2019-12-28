<?php

class Module_SettingsContainer_Children extends Module_Basic_Children
{
	protected function makeCreateInput()
	{
		return (string) new Fragment_Button_Create("Nieuwe setting", $this->getLifeId(), "Setting");
	}
	
	public function childTypeIsAllowed($module)
	{
		return ($module == "Setting") ;
	}
	
	protected function listOrder()
	{
		return "u_cores.name DESC";
	}
	
	protected function headers(array $headers = array())
	{
		$headers = array_merge(array(
			"settingValue" => "",
		),$headers);
		return parent::headers($headers);
	}
	
	protected function listExtention(array $select = array(), array $join = array(), $where = "")
	{
		$select = array_merge(array("u_settings.value as settingValue"), $select);
		$join = array_merge(array( array("table" => "u_settings", "using" => "ID") ), $join);
			
		return parent::listExtention($select, $join, $where);
	}
}