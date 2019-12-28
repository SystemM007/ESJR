<?php

class Editable_SelectBasic extends Editable_Abstract
{
	private $optionData;
	private $valueField;
	private $displayField;
	
	public function __construct($name, $dataField = array())
	{
		parent::__construct("Select", $name, $dataField);
	}
	
	public function makeEditable()
	{
		$this->options["selectOptions"] = $this->getSelectOptions();
	
		return parent::makeEditable();
	}
	
	public function optionData($optionData)
	{
		if(is_array($optionData)) $optionData = new Dataset($optionData);
		
		if(!$optionData->isColumn("value")) throw new Exception("optionsData moet een kolom value hebben");
		if(!$optionData->isColumn("display")) throw new Exception("optionsData moet een kolom display hebben");
		if(!$optionData->isColumn("selected")) throw new Exception("optionsData moet een kolom selected hebben");
		
		$this->optionData = $optionData;
	}
			
	// overrule parent om zo dispayField te gebruiken ipv field
	protected function getValue()
	{
		if($this->useValue) return $this->useValue;
		
		foreach($this->optionData as $row)
		{
			if($row["selected"]) return $row["display"];
		}
		//throw new Exception("Geen true gevonden in selected kolom");
		return "selecteer...";
	}

	protected function getSelectOptions()
	{	
		if($this->useSelectOptions)
		{
			return $this->useSelectOptions;
		}
		else
		{	
			$selectOptions = " "; // hmm tja hekje (mag weg)
			foreach($this->optionData as $row) 
			{
				$selectOptions .= "<option value=\"";
				$selectOptions .= $row["value"];
				$selectOptions .= "\"";
				$selectOptions .= $row["selected"] ? "selected=\"selected\"" : "";
				$selectOptions .= ">";
				$selectOptions .= $row["display"];
				$selectOptions .= "</option>";
			}
			
			return $selectOptions;
		}
		
		return "";
	}
}
	