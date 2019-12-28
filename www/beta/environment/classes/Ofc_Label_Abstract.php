<?php
class Ofc_Label_Abstract extends Ofc_Object
{
	protected $data = array(
		"colour" => "", // colour
		"size" => 10,
		"rotate" => "horizontal"
	);
	
	public function setColour( $colour )
	{
		$this->data["colour"] = (string) new Ofc_Colour($colour);
	}
	
	public function setSize( $size )
	{
		$this->data["size"] = (int) $size;
	}
	
	public function setRotate( $rotate )
	{
		if(! in_array($rotate, array("diagonal", "vertical", "horizontal")))
		{
			throw new Exception("The value of rotate is invalid: '$rotate'. Use diagonal, vertical, or horizontal ");
		}
		$this->data["rotate"] = $rotate;
	}
	
	public function setVertical()
	{
		$this->setRotate("vertical");
	}
	
	public function setHorizontal()
	{
		$this->setRotate("horizontal");
	}
	
	public function setDiagonal()
	{
		$this->setRotate("diagonal");
	}
	
}