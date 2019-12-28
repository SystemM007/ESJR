<?php

class Template_Root_Reader extends Template_Collector_Reader
{
	public function makeElement()
	{	
		$version = $this->attributes["version"];
		if(!isset($version)) throw new Template_Exception("De template tag bevat geen version tag", $this);
		$version = (float) $version;
		
		return new Template_Root_Element($this->elements, $version);
	}
}