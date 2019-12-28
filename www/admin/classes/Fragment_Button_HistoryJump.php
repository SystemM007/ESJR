<?php

class Fragment_Button_HistoryJump extends Fragment_Button_Page
{
	protected $makeButton = true;

	public function __construct($offset = 1)
	{
		if($offset < 0) throw new Exception("Offset mag niet negatief zijn", E_USER_ERROR);
		
		$history = History::get($offset);
		
		if(!$history)
		{
			$this->makeButton = false;
		}
		else
		{				
			$value = $history["name"];
			$pageName = "HistoryJumpPage";
			$data = $offset;
			
			$this->setClass("historyJump");
	
			parent::__construct($value, $pageName, $data);
		}
	}
	
	public function create()
	{
		if($this->makeButton) return parent::create();
		else return "";
	}
}