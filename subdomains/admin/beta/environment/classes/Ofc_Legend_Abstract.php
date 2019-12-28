<?php
class Ofc_Legend_Abstract extends Ofc_Object
{
	protected $data = array(
		"text" => "",
		// hasn't flash a default value for style?
		"style" => "{font-size: 20px; color:#0000ff; font-family: Verdana; text-align: center;}",
	);
	
	public function __construct($text = null)
	{
		if(isset($text)) $this->setText($text);
	}
	
	public function setText($text)
	{
		$this->data["text"] = (string) $text;
	}
	
	public function setStyle($style)
	{
		//@todo validate style?
		$this->data["style"] = (string) $style;
	}
}
?>