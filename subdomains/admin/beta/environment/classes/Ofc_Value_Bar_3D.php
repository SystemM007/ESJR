<?php
class Ofc_BarValue_3D extends Ofc_BarValue_Abstract
{
	public function __construct($top)
	{
		$this->addDataFields(array(
			"top" => $top,
			"colour" => "", //colour
			"tip" => "",
		));
	}
	
	public function setColour($colour)
	{
		$this->data["colour"] = (string) new Ofc_Colour($colour);
	}
	
	public function setTip($tip)
	{
		$this->data["tip"] = (string) $tip;
	}
}