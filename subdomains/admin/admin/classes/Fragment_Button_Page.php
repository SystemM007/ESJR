<?php

class Fragment_Button_Page extends Fragment_Button_Basic {
	
	public function __construct($value, $pageName, $data = array(), $options = array(), $confirm = ""){
	
		$onClick = new Fragment_JS_Page($pageName, $data, $options, $confirm);
		
		parent::__construct($value, $onClick);
	
	
	}
}