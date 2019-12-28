<?php

abstract class Template_Collector_Reader extends Template_Abstract_Reader
{
	protected function addTag($tagName, array $attributes, $innerContent)
	{
		switch($tagName)
		{
			case "location" : $tplClass = "Location";
			break;
			
			case "conditional" : $tplClass = "Conditional";
			break;

			case "namespace" : $tplClass = "Namespace";
			break;

			case "multifill" : $tplClass = "Multifill";
			break;
			
			default : throw new Template_Exception("Onbekende tag '$tagName' onder " . get_class($this), $this);
		}
		
		$Reader = $this->createReader($tplClass, $attributes, $innerContent);
		$this->addElement($Reader->makeElement());
	}
	
	protected function createReader($tplClassType, $attributes, $innerContent)
	{
		$class = "Template_" . $tplClassType . "_Reader";
		return new $class($attributes, $innerContent, $this->getFile(), $this->getReadLine());
	}
	
	protected function addString($string)
	{
		$this->addElement($string);
	}
}