<?php

class Fragment_Button_ServerAction extends Fragment_Button_Basic {
	
	public function __construct($value, ServerAction $ServerAction, $options = array(), $confirm = "")
	{
		$onClick = (string) new Fragment_JS_ServerAction($ServerAction, $options, $confirm);
		parent::__construct($value, $onClick);
	}
}