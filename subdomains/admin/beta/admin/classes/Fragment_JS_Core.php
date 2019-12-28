<?php

class Fragment_JS_Core extends Fragment_JS_Basic {

	public function __construct($ID, $data = array(), $options = array(), $confirm = false)
	{
		// dit zo laten?!
		if(!is_array($data)) $data = array("id" => $data);
		// ..
		
		$this->setFunction("new Request.Core");
		$this->setArgs($ID, $data, $options);
		$this->setConfirm($confirm);
		
		parent::__construct();
	
	}
}