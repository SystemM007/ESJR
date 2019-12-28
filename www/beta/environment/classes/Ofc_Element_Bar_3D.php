<?php
class Ofc_Element_Bar_3D extends Ofc_Element_Bar_Abstract
{
	public function __construct(array $values = array())
	{
		parent::__construct("bar_3d", $values, array());
	}
	
	protected function valueObjectValid($className)
	{
		// @todo check classname
		return $className instanceof Ofc_Value_Bar_3d;
	}
}