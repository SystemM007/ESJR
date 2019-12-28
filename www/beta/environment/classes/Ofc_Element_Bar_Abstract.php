<?php

abstract class Ofc_Element_Bar_Abstract extends Ofc_Element_Abstract
{
	public function __construct($type, array $values = array(), array $data = array())
	{
		/*
		 * @todo moet hier geen text bij?!
		 */
		parent::__construct(array(
			"type" => $type,
			"font-size" => 14, // int?
			"colour" => "", // color
			"alpha" => 100, // int or string?
			"values" => array(),
		));
		
		if($values) $this->addValues($values);
		if($data) $this->addDataFieds($data);
	}
	
	public function setFontSize($fontSize)
	{
		$this->data["font-size"] = (int) $fontSize;
	}
	
	public function setColour($colour)
	{
		$this->data["colour"] = (string) new Ofc_Colour($colour);
	}
	
	public function setAplha($alpha)
	{
		$this->data["alpha"] = (int) $alpha;
	}
	
	public function addValues(array $values)
	{
		// @todo check if value can be an float
		foreach($values as $value)
		{
			if(is_object($value) && ! $this->valueObjectValid(get_class($value))) 
			{
				throw new Exception("Value is an object wich is not valid."); 
			}
			else
			{
				$value = (int) $value;
			}
			$this->data["values"][] = $value;
		}
	}
	
	abstract protected function valueObjectValid($className);
}