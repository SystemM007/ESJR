<?php

class Fragment_Button_Reset extends Fragment_Button_Basic
{
	public function __construct($value = "Reset")
	{
		$res = new Fragment_JS_Basic();
		$res->setFunction("EditableReg.cancelAll");
		
		$this->setClass("reset");
		
		parent::__construct($value, $res);
	}
}