<?php

class Fragment_Button_Basic extends Fragment_Abstract {

	private $value;
	private $onClick;
	private $class;
	
	public function __construct($value = "NoName", $onClick = "alert(\"No Action\")"){
		
		$this->value = (string) $value;
		$this->onClick = (string) htmlspecialchars($onClick);
	}
	
	protected function setClass($class)
	{
		$this->class = $class;
	}
	
	public function create()
	{
		$html = "";
		$html .= "<button type=\"button\""; // LETOP!!!!!!!! type=button moet, anders wordt het een submit
		if($this->class) $html .= "class=\"$this->class\" ";
		$html .= "onclick=\"$this->onClick\">$this->value</button>";
		
		return $html;
	
	}	
}
?>