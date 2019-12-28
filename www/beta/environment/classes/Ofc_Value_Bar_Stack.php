<?php
class Ofc_BarValue_Stack extends Ofc_BarValue_Abstract
{
	/*
	 * @todo: data type val?! Array?
	 */
	public function __construct($val, $colour)
	{
		$this->valaddDataFields(array(
			"val" => $val,
			"colour" => "", //colour
		));
		
		$this->setColour($colour);
	}
	
	public function setColour($colour)
	{
		$this->data["colour"] = (string) new Ofc_Colour($colour);
	}
}