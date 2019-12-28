<?php

class Fragment_Button_Action extends Fragment_Button_Basic {
	
	public function __construct($value, $lifeId, $action, $data = array(), $options = array(), $confirm = "")
	{
		$onClick = (string) new Fragment_JS_Action($lifeId, $action, $data, $options, $confirm);
		parent::__construct($value, $onClick);
	}
}