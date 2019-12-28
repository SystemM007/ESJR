<?php

class Fragment_Button_Core extends Fragment_Button_Basic {
	
	public function __construct($value, $ID, $data = array(), $options = array(), $confirm = ""){
	
		$onClick = new Fragment_JS_Core($ID, $data, $options, $confirm);
		
		parent::__construct($value, $onClick);
	
	
	}
}