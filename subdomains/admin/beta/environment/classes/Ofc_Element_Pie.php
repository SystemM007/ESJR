<?php
class Ofc_Element_Pie extends Ofc_Element_Abstract
{
	 public function __construct(array $values)
	 {
	 	parent::__construct("pie", array(
	 		"colours" => array(),
	 		"alpha" => 0.6, // float
	 		"border" => 2, // int
	 		"values" => $values	
	 	));
	 }
	 
	 public function setColours(array $colours)
	 {
	 	foreach($colours as $colour) $this->data["colours"][] = (string) new Ofc_Colour($colour);
	 }
	 
	 public function setAlpha($alpha)
	 {
	 	$this->data["alpha"] = (float) $alpha;
	 }
	 
	 public function setBorder($border)
	 {
	  	$this->data["border"] = (int) $border;
	 }
}
