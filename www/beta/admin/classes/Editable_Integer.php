<?php

class Editable_Integer extends Editable_Text
{
	protected function checkInput($value)
	{
		if(!InputValidation::isInteger($value)) throw new Editable_Exception("Dit moet een geheel getal zijn. Gebruik enkel cijfers.");
	}
	
	protected function rewriteInputPost($value)
	{
		$value = intval($value);
		return $value;
	}
}