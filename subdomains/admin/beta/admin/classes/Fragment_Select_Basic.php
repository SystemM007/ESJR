<?php

class Fragment_Select_Basic extends Fragment_Abstract {

	private $data;
	private $idField;
	private $nameField;
	private $onChange;
	private $command;

	public function __construct(Matrix $data, $idField, $nameField, $onChange, $command = "Maak een keuze: ")
	{
		$this->data = $data;
		$this->idField = $idField;
		$this->nameField = $nameField;
		$this->onChange = $onChange;
		$this->command = $command;
	}
	
	public function create()
	{
		$str = "";
		
		$str .= "<select onchange='if(this.selectedIndex != 0){" . $this->onChange . ";}; this.selectedIndex = 0; '>";
		
		$str .= "<option selected>" . $this->command . "</option>" ;
		//$str .= "<optgroup label='" . $this->command . "'>" ;
	
		foreach($this->data as $row)
		{
			$str .= "<option value='" .
			$row[$this->idField] .
			"'>" .
			$row[$this->nameField] .
			"</option>";
		}
		
		//$str .= "</optgroup>" ;
		$str .= "</select>" ;
		
		
		return $str;
	}
}