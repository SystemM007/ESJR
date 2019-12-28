<?php

class Fragment_Button_Login extends Fragment_Button_Action
{
	public function __construct($lifeId, $data = array(), $options = array())
	{
		$options = array_merge(array("loadEditables" => true), $options);

		$this->setClass("login");
		
		parent::__construct("Login", $lifeId, "login" , $data, $options);
	}
}