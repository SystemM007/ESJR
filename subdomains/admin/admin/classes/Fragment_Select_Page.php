<?php

class Fragment_Select_Page extends Fragment_Select_Basic
{
	public function __construct(Matrix $selectorData, $idField, $nameField, $page, $data = array(), $options = array(), $command = "Maak een keuze: ")
	{
		// nodig??
		$data = array_merge($data, array("id" => "THIS_VALUE"));
		
		$action = (string) new Fragment_JS_Page($page, $data, $options);
		
		$onChange = str_replace('"id":"THIS_VALUE"', '"id":this.value', $action);
		
		parent::__construct($selectorData, $idField, $nameField, $onChange, $command);
	}
}