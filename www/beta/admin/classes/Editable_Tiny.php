<?php

class Editable_Tiny extends Editable_Abstract
{
	public function __construct($name, array $dataField = array(), array $tinySettings = array())
	{
		parent::__construct("Tiny", $name, $dataField);
		
		$this->options["tinySettings"] = $tinySettings;
	}
	
	protected function rewriteInput($value)
	{
		return  StringFunctions::replaceMailto($value);
	}
}
	