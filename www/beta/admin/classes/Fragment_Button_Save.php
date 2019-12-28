<?php

class Fragment_Button_Save extends Fragment_Button_ServerAction
{
	public function __construct(Core $Core, $options = array())
	{
		$options = array_merge(array("loadEditables" => true), $options);

		$this->setClass("save");
		
		parent::__construct("Opslaan", new ServerAction(array($Core->Edit, "save")) , $options);
	}
}