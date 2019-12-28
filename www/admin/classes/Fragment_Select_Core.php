<?php

class Fragment_Select_Core extends Fragment_Select_Basic
{
	public function __construct(Matrix $selectorData, $idField, $nameField, $ID, $data = array(), $options = array(), $command = "Maak een keuze: ")
	{
		$action = (string) new Fragment_JS_Core($ID, $data, $options);
		
		$onChange = str_replace('"id":"THIS_VALUE"', '"id":this.value', $action);
		
		parent::__construct($selectorData, $idField, $nameField, $onChange, $command);
	}
}