<?php

class Editable_Date extends Editable_Abstract
{
	public function __construct($name, $dataField = array())
	{
		parent::__construct("Text", $name, $dataField);
	}
	
	protected function getValue()
	{
		$value = parent::getValue();
		return StringFunctions::date($value);
	}
	
	protected function checkInput($value)
	{
		list($d, $m, $y) = explode("-", $value); // het moet eruit zien als dag-maand-jaar;
		
		if(!checkdate((int) $m, (int) $d, (int) $y) ) throw new Editable_Exception("De datum moet worden ingevoerd als 'dag-maand-jaar'");
		
		$time = @mktime(0, 0, 0, $m, $d, $y);
		if($time === false || $time === -1) throw new Editable_Exception("Er is een onjuiste datum ingegeven: '$d-$m-$y', de datum moet worden ingevoerd als 'dag-maand-jaar");
		
		if($this->check["dateFuture"])
		{
			if($time < time()) throw new Editable_Exception("De datum moet in de toekomst liggen");
		}
	}
	
	protected function rewriteInputPost($value)
	{
		list($d, $m, $y) = explode("-", $value); // het moet eruit zien als dag-maand-jaar;
		return  @mktime(0, 0, 0, $m, $d, $y);
	}
	
	public function checkDateFuture($set = true)
	{
		$this->check["dateFuture"] = $set;
	}
}
	