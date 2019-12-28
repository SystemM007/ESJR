<?php

class Fragment_JS_ServerActionSelect extends Fragment_JS_Basic
{

	public function __construct($options, $confirm = false)
	{
		$this->setFunction("new Request.ServerAction");
		$this->addArg("this.value", false);
		$this->addArg($options);
		$this->setConfirm($confirm);
	}
}