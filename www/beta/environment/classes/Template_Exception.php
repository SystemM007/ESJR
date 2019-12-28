<?php

class Template_Exception extends Exception
{
	protected $Reader;
	
	public function __construct($message, Template_Abstract_Reader $Reader)
	{
		$this->Reader = $Reader;			
		parent::__construct($message);	
	}
	
	public function __toString()
	{
		return
			"\n<strong>Template read error</strong>: " . $this->getMessage()
			. "\n<br />in <strong>" . $this->Reader->getFile() . "</strong> on line <strong>" . (string) $this->Reader->getReadLine() . "</strong>"
			//. vds($this->Reader)
			;

	}
}