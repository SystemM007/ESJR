<?php

class Template_Location_Reader extends Template_Abstract_Reader
{
	protected function addTag($tagName, array $attributes, $innerContent)
	{
		throw new Template_Exception("In een Location mogen geen tag worden geplaatst", $this);
	}
	
	protected function addString($string)
	{
		$this->addElement($string);
	}
	
	public function makeElement()
	{	
		if(!isset($this->attributes["key"])) throw new Template_Exception("Een Location tag moet een key attribuut bevatten", $this);
	
		return new Template_Location_Element($this->attributes["key"], $this->elements[0]);
	}
}