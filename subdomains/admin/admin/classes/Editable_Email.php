<?php

class Editable_Email extends Editable_Text
{
	protected function checkInput($value)
	{
		if(!InputValidation::isEmail($value))
		{
			throw new Editable_Exception("Het gegeven email adres '$value' lijkt ongeldig te zijn!");
		}
	}

}
	