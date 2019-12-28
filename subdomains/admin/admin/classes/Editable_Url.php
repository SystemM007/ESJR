<?php

class Editable_Url extends Editable_Abstract
{
	public function __construct($name, $dataField = array())
	{
		parent::__construct("Text", $name, $dataField);
	}
	
	protected function getValue()
	{
		$value = parent::getValue();
		$value = htmlspecialchars($value);
		return $value;
	}

	protected function checkInput($value)
	{
		$regex = "/(?<protocol>http(s)?|ftp):\/\/(?<server>([A-Za-z0-9-]+\.)*(?<basedomain>[A-Za-z0-9-]+\.[A-Za-z0-9]+))+((\/?)(?<path>(?<dir>[A-Za-z0-9\._\-]+)(\/){0,1}[A-Za-z0-9.-\/]*)){0,1}/i";
			
		if(!preg_match($regex, $value)) throw new Editable_Exception("Dit is geen geldige URL");
	}
	
	protected function rewriteInput($value)
	{
		return rawurldecode($value);
	}
}