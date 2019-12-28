<?php

class Editable_Float extends Editable_Text
{
	protected function checkInput($value)
	{
		if(!preg_match("/^\d+(,\d*|.\d*|)$/", $value))
		{
			throw new Editable_Exception("Dit is geen geldig getal. Gebruik alleen cijfers en ŽŽn komma of punt");
		}
	}
	
	protected function rewriteInputPost($value)
	{
		$value = str_replace(",", ".", $value);
		$value = floatval($value);
		return $value;
	}
}
	