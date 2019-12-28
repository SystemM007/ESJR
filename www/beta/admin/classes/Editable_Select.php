<?php

class Editable_Select extends Editable_SelectBasic
{
	private $optionTable = array();
		
	public function optionTable($optionTable, $valueField, $displayField, $conditions = NULL)
	{
		// selecteert een lijst met waarden - namen, 
		// en een true/false kolom 'selected' die aangeeft of de waarde overeenkomt met  de waarde het betreffende item.
		
		$conditions = $conditions ? "WHERE $conditions" : "";
		
		$data = MySql::select("
			SELECT 
				$optionTable.$valueField AS value, 
				$optionTable.$displayField AS display, 
				$this->table.$this->field = $optionTable.$valueField AS selected
			FROM $optionTable
			LEFT JOIN $this->table ON $this->table.$this->idField = '$this->dataId'
			$conditions
		");
			
		parent::optionData($data, $valueField, $displayField);
	}
}
	