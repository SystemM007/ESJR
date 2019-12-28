<?php
class Ofc_Line_Hollow extends Ofc_Line_Abstract
{
	public function __construct($text = NULL, array $values = array())
	{
		parent::__construct("line_hollow", $text, $values, array());
	}
}
?>