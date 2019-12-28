<?php

class Editable_Passwd extends Editable_Abstract
{
	public function __construct($name, $dataField = array())
	{
		parent::__construct("Password", $name, $dataField);
	}
}
	