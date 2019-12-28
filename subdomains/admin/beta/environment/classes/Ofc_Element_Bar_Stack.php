<?php
class Ofc_Element_Bar_Stack extends Ofc_Element_Bar_Fun
{
	public function __construct(array $values = array())
	{
		parent::__construct("bar_sketch", $values, array());
	}
	
	protected function valueObjectValid($className)
	{
		// @todo check classname
		return $className instanceof Ofc_Value_Bar_3d;
	}
}