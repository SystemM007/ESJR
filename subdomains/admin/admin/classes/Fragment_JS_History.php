<?php

class Fragment_JS_History extends Fragment_JS_Basic {

	public function __construct($offset, $data = array(), $options = array(), $confirm = false)
	{
		$this->setFunction("new Request.History");
		$this->setArgs($offset, $data, $options);
		$this->setConfirm($confirm);
		
		parent::__construct();
	
	}
}