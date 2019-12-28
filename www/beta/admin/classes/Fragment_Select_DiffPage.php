<?php

class Fragment_Select_DiffPage extends Fragment_Select_Basic
{
	public function __construct(Matrix $selectorData, $pageField, $nameField, $data = array(), $options = array(), $command = "Maak een keuze: ")
	{
		$action = (string) new Fragment_JS_Page("THIS_PAGE", $data, $options);
		
		$onChange = str_replace('"THIS_PAGE"', 'this.value', $action);
		
		parent::__construct($selectorData, $pageField, $nameField, $onChange, $command);
	}
}