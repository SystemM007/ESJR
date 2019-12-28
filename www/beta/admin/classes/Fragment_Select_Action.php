<?php

class Fragment_Select_Action extends Fragment_Select_Basic
{
	public function __construct(Matrix $selectorData, $idField, $nameField, $lifeId, $action, array $data = array(), array $options = array(), $command = "Maak een keuze: ")
	{
		$data = array_merge($data, array("id" => "THIS_VALUE"));
		
		$action = (string) new Fragment_JS_Action($lifeId, $action, $data, $options);
		
		$onChange = str_replace('"id":"THIS_VALUE"', '"id":this.value', $action);
		
		parent::__construct($selectorData, $idField, $nameField, $onChange, $command);
	}
	
	
}