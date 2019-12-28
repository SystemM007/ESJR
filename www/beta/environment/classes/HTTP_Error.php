<?php 

abstract class HTTP_Error
{
	final public function __construct($message)
	{
		$this->sendHeader();
		
		$this->sendBody($message);
		
		exit;
	}
	
	abstract protected function sendHeader();
	
	/*
	 * overrule with template
	 */
	protected function sendBody()
	{
		echo $message;
	}
}

?>