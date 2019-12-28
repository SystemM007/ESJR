<?php
abstract class Ofc_Element_Line_Abstract extends Ofc_Data
{
	public function __construct($type, $text = NULL, array $values = array(), array $data = array())
	{
		parent::__construct($type, array(
			"text" => "",
			"font-size" => 10,
			"values" => array(),
			"width" => null, // int
			"color" => null, // color
			// @todo check if this is valid with a normal line
			"dot-size" => null,
			"halo-size" => null,
		));

		if(isset($text)) $this->setText($data);
		if($values) $this->setvalues($data);
		if($data) $this->addDataFields($data);
	}
	
	public function setText($text)
	{
		$this->data["text"] = (string) $text;		
	}
	
	public function setvalues( array $values )
	{
		$this->data["values"] = $values;		
	}
	
	public function setWidth( $width )
	{
		$this->data["width"] = (int) $width;		
	}
	
	public function setColour( $colour )
	{
		$this->data["colour"] = new Ofc_Colour($colour);
	}
	
	public function setDotSize( $size )
	{
		$this->data["dot-size"] = (int) $size;		
	}
	
	public function setHaloSize( $size )
	{
		$this->data["halo-size"] = (int) $size;		
	}
}
?>