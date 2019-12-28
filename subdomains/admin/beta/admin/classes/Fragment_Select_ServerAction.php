<?php

class Fragment_Select_ServerAction extends Fragment_Select_Basic
{
	public function __construct(Matrix $selectorData, $idField, $nameField, array $options = array(), $command = "Maak een keuze: ", $confirm)
	{
		$onChange = new Fragment_JS_ServerActionSelect($options, $confirm);
		
		parent::__construct($selectorData, $idField, $nameField, $onChange, $command);
	}
	
	
}