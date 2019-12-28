<?php

class Editable_Text extends Editable_Abstract
{
	public function __construct($name, $dataField = array())
	{
		parent::__construct("Text", $name, $dataField);
	}
	
	protected function rewriteInput($value)
	{
		$value = htmlspecialchars($value);
	
		return $value;
	}
}
	