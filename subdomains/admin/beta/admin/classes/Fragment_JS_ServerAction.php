<?php

class Fragment_JS_ServerAction extends Fragment_JS_Basic {

	public function __construct(ServerAction $ServerAction, $options = array(), $confirm = false)
	{
		$this->setFunction("new Request.ServerAction");
		$this->setArgs((string) $ServerAction, $options);
		$this->setConfirm($confirm);
	}
}