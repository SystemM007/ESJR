<?php
class Ofc_Axis_Y extends Ofc_Axis_Abstract
{
	public function __construct()
	{			
		$this->data = array_merge($this->data, array(
			"labels" => array() 
		));
	}
	
	public function setLabels(array $labels)
	{
		$this->data["labels"] = $labels;
	}	
}