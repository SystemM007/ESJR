<?php

class Ofc_Label_Element extends Ofc_Label_Abstract
{
	public function __constuct( $text = null, $colour = null, $size = null, $rotate = null)
	{
		$this->data = array_merge($this->data, array(
			"text" => 0,
			"visible" => false, // give bool true to force displaying
		));
		
		if(isset($text)) $this->setText( $text );
		if(isset($colour)) $this->setColour( $colour );
		if(isset($size)) $this->setSize( $size );
		if(isset($rotate)) $this->setRotate( $rotate );
	}
	
	public function setText( $text )
	{
		$this->text = (string) $text;
	}
	
	public function setVisible($visible = true)
	{
		$this->data["visible"] = (bool) $visible;
	}
}