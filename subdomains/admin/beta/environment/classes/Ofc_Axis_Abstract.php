<?php
abstract class Ofc_Axis_Abstract extends Ofc_Object
{
	protected $data = array(
		"stroke" => 0,
		"colour" => "", // colour
		"grid-colour" => "", // colour
		"offset" => 1, // int or boolean?!
		"steps" => 1,
		"3d" => false,
		"labels" => null, // for the X axis this is an object of Ofc_Labels, for the Y and the YRight it is an array
		"min" => 0,
		"max" => 1,
	);
	
	public function setStroke($stroke)
	{
		$this->data["stroke"] = (int) $stroke;
	}
	
	public function setColour($colour)
	{
		$this->data["colour"] = new Ofc_Colour($colour);
	}
	
	public function setGridColour($colour)
	{
		$this->data["grid-colour"] = (string) new Ofc_Colour($colour);
	}
	
	public function setOffset($offset)
	{
		$this->data["offset"] = $offset ? 1 : 0;
	}
	
	public function setSteps($steps)
	{
		$this->data["steps"] = (int) $steps;
	}
	
	public function set3d($threeD)
	{
		$this->data["3d"] = (bool) $threeD;
	}
		
	public function setMin($min)
	{
		$this->data["min"] = (int) $min;
	}
	
	public function setMax($max)
	{
		$this->data["max"] = (int) $max;
	}
	
	/*
	 * Combined functions
	 */
	
	public function setRange($min, $max, $steps)
	{
		$this->setMin($min);
		$this->setMax($max);
		$this->setSteps($steps);
	}
	
	public function setLabelsFromArray(array $labels)
	{
		// @todo update Classname
		$Labels = new x_axis_labels();
		$Labels->set_labels( $labels );
		
		if($this->data["steps"]) $x_axis_labels->set_steps( $this->steps );

		$this->setLabels($Labels);
	}
	
	public function setColours($colour, $gridColour)
	{
		$this->setColour($colour);
		$this->setGridColour($gridColour);
	}
	
}