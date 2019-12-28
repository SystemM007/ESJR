<?php

class Editable_ClassName extends Editable_Text
{
	protected $underscoreAllowed = false;

	protected function onConstruct()
	{
		$this->checkMaxLength(100);
	}

	protected function checkInput($value)
	{
		if($this->underscoreAllowed)
		{
			if(!preg_match("/^[a-zA-Z0-9_]+$/", $value)) throw new Editable_Exception("Een klasse naam mag alleen normale letters en cijfers en underscores bevatten.");
		}
		else
		{
			if(!preg_match("/^[a-zA-Z0-9]+$/", $value)) throw new Editable_Exception("Een klasse naam mag alleen normale letters en cijfers bevatten.");
		}
	}
	
	public function setUnderscoreAllowed($set = true)
	{
		$this->underscoreAllowed = $set;
	}
}