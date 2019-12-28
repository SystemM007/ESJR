<?php

class Fragment_Button_Delete extends Fragment_Button_ServerAction
{
	public function __construct(Core $Core, $objectName = "", $memo = array(), $options = array(),  $value = NULL)
	{
		if(!isset($value))
		{
			$value = "";
			$this->setClass("delete");
		}
		
		if($objectName) $confirm = "Weet u zeker dat u $objectName wilt verwijderen?";
		
		$ServerAction = new ServerAction(array($Core->Children, "delete"), $memo);
		
		parent::__construct($value, $ServerAction, $options, $confirm);
	}
}