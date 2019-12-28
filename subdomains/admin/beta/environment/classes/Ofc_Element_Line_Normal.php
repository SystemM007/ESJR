<?php
class Ofc_Line_Normal extends Ofc_Line_Abstract
{
	public function __construct($text = NULL, array $values = array(), array $data = array())
	{
		parent::__construct("line", $text, $values, $data, array());
	}
}
?>