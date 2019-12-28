<?php

class Editable_UriPart extends Editable_Abstract
{
	public function __construct($name, $dataField = array())
	{
		parent::__construct("Text", $name, $dataField);
	}
	
	protected function getValue()
	{
		return rawurldecode(parent::getValue());
	}

	protected function rewriteInput($value)
	{
		$value = rawurlencode($value);
		return $value;
	}
}