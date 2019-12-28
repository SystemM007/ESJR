<?php

class Fragment_JS_Action extends Fragment_JS_Basic {

	public function __construct($lifeId, $action, $data = array(), $options = array(), $confirm = false)
	{
		if(!is_array($data)) $data = array("id" => $data);

		$this->setFunction("new Request.Action");
		$this->setArgs($lifeId, $action, $data, $options);
		$this->setConfirm($confirm);
		
		parent::__construct();
	
	}
}