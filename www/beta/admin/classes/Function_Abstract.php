<?php

abstract class Function_Abstract
{
	protected $accessLevel = User::ACCESSLEVEL_ALWAYS;

	final public function __construct()
	{
		if(!User::levelAllowed($this->accessLevel))
		{
			throw new Exception("User met level '" . User::getLevelName() ."' heeft geen toegang tot speciale functie '" . get_class($this) . "' met toegangs niveau '" . User::getAccessLevelName($accessLevel) . "'");
		}
		
		$this->startFunction();
	}
	
	abstract protected function startFunction();

}