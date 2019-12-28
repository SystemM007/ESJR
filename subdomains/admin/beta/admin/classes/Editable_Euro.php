<?php
class Editable_Euro extends Editable_Float
{
	protected function checkInput($value)
	{
		if(!preg_match("/^\d+(,\d{1,2}|.\d{1,2}|)$/", $value))
		{
			throw new Editable_Exception("Een bedrag moet een gewoon getal zijn met niet meer dan twee decimalen hebben.");
		}
	}
}