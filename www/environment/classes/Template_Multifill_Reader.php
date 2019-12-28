<?php

class Template_Multifill_Reader extends Template_Collector_Reader
{
	public function makeElement()
	{	
		if(!isset($this->attributes["key"]))
		{
			throw new Template_Exception("Multifill tag moet een key attribuut hebben", $this);
		}
		
		return new Template_Multifill_Element($this->elements, $this->attributes["key"]);
	}
}