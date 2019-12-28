<?php
class Editable_MDate extends Editable_Date
{
	protected function rewriteInputPost($value)
	{
		list($d, $m, $y) = explode("-", $value); // het moet eruit zien als dag-maand-jaar;
		return date("y-m-d", mktime(0, 0, 0, $m, $d, $y)); // het gaat eruit zien als jaar-maand-dag;
	}
}