<?php

abstract class Section_Abstract
{
	// dit wil je private krijgen.
	protected $ID;
	
	public function __construct($ID)
	{
		$this->ID = $ID;
	}
	
	public function getID()
	{
		return $this->ID;
	}
	
	// waar dit nou lollig voor is?
	public function finish()
	{
	}
	
}