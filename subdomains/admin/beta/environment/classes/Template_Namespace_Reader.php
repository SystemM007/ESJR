<?php

class Template_Namespace_Reader extends Template_Collector_Reader
{
	public function makeElement()
	{	
		if(!isset($this->attributes["key"]))
		{
			throw new Template_Exception("Namespace tag moet een key attribuut hebben", $this);
		}
		
		return new Template_Namespace_Element($this->elements, $this->attributes["key"]);
	}
}