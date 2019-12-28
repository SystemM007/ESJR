<?php

class Function_LogOff extends Function_Abstract
{
	protected $accessLevel = User::ACCESSLEVEL_ALWAYS_NO_LOGIN;

	protected function startFunction()
	{	
		User::writeCookie("", "", -42000);
		Session::destroy();
		
		header("Location: " . Uri::admin);
		exit;
	}
}
	