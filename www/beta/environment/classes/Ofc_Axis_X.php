<?php
class Ofc_Axis_X extends Ofc_Axis_GridController
{
	public function __construct()
	{
		$this->data = array_merge($this->data, array(
			"labels" => null //Ofc_Label_Set // @todo update classname
		));
	}
	
	public function setLabels(Ofc_Label_Set $Labels)
	{
		$this->data["labels"] = $Labels->getData();
	}	
}