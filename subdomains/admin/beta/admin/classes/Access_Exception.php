<?php

class Access_Exception extends Exception
{
	protected $accessLevel; 
	protected $userLevel;
	protected $trace;
	protected $id;
	
	public function __construct($context, $accessLevel = NULL, $userLevel = NULL, $id = NULL)
	{
		parent::__construct("Geen toegang in context '$context'");
		
		$this->accessLevel = $accessLevel;
		$this->userLevel = isset($userLevel) ? $userLevel : User::getLevel();
		$this->id = $id;
	}
	
	public function __toString()
	{
		$error = $this->getMessage() . "\n".
		$error .= "Userlevel: '" . User::getLevelName($this->userLevel) . "'\n" ;
		if(isset($this->accessLevel)) $error .= "AccessLevel: '" . User::getAccessLevelName($this->accessLevel) . "'\n" ;
		$error .= "(Core of Module) id: $this->id\n\n";
		$error .= backtraceString($this->getTrace());
		
		return $error;
	}
}