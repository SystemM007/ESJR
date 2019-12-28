<?php

class Fragment_Select_DiffCore extends Fragment_Select_Basic
{
	public function __construct(Matrix $selectorData, $IDField, $nameField, $data = array(), $options = array(), $command = "Maak een keuze: ")
	{
		$action = (string) new Fragment_JS_Core("THIS_CORE", $data, $options);
		
		$onChange = str_replace('Request.Core("THIS_CORE",', 'Request.Core(this.value,', $action);
		
		parent::__construct($selectorData, $IDField, $nameField, $onChange, $command);
	}
}