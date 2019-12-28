<?php
abstract class Ofc_Element_Abstract extends Ofc_Object
{
	public function __construct($type, $data = NULL)
	{
		parent::__construct(array(
			"type" => $type, // @todo test if type is valid
		));
		
		if(isset($data)) $this->addDataFields($data);
	}
}
?>