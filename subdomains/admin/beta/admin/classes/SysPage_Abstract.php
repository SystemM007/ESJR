<?php

abstract class SysPage_Abstract extends Module_Life
{
	protected $accessLevel = User::ACCESSLEVEL_ALWAYS;

	public function isRequestable($function)
	{
		return false;
	}
	
	final public function __construct()
	{
		if(!User::levelAllowed($this->accessLevel)) throw new Access_Exception("Laden van systeempagina '" . get_class($this) ."'");
		$this->construct();
	}
	
	abstract protected function construct();
}