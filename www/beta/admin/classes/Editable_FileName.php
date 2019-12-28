<?php

class Editable_FileName extends Editable_Abstract
{
	public function __construct($name, $dataField = array())
	{
		parent::__construct("Text", $name, $dataField);
		
		$this->check["encode"] = true;
	}
	
	protected function getValue()
	{
		$value = parent::getValue();
		return urldecode($value);
	}
	
	protected function rewriteInput($value)
	{
		/*
		* als er WEL geencodeerd moet worden opgeslagen: NIETS aan doen
		* want de waarde komt al geencodeerd binnen!
		*/
		
		if(!$this->check["encode"]) $value = rawurldecode($value); // decoden vanaf text editable
		
		return $value;
	}
	
	protected function checkInput($value)
	{
	}
	
	public function doEncode($set = true)
	{
		$this->check["encode"] = $set;
	} 
}