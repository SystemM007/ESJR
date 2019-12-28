<?php

abstract class Fragment_Abstract
{
	abstract public function create();
	
	final public function __toString()
	{
	
		try
		{
			$string = $this->create();
			if(!is_string($string)) throw new Exception("Geen string ontvangen van create functie!");
		}
		catch(Exception $e)
		{
			die("Create functie werpt error: " . $e->getMessage() );
		}
		
		return $string;
	}
}
