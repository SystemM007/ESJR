<?php

class Editable_Bool extends Editable_Abstract
{

	public function __construct($name, $dataField = array())
	{
		parent::__construct("Select", $name, $dataField);
	}
	
	protected function getValue()
	{
		$valueOriginal = (bool) parent::getValue();
		
		$value = $valueOriginal ? "Ja" : "Nee";
		
		$this->options["selectOptions"] =
			"<option value=\"1\" " . ($valueOriginal ? "selected=\"selected\"" : "") . ">Ja</option>" .
			"<option value=\"0\" " . (!$valueOriginal ? "selected=\"selected\"" : "") . ">Nee</option>";
		
		return $value;
	}
}