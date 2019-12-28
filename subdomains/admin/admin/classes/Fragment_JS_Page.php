<?php

class Fragment_JS_Page extends Fragment_JS_Basic {

	public function __construct($pageName, $data = array(), $options = array(), $confirm = false){
	
		if(!is_array($data)) $data = array("id" => $data);

		$this->setFunction("new Request.Page");
		$this->setArgs($pageName, $data, $options);
		$this->setConfirm($confirm);
		
		parent::__construct();
	
	}
}