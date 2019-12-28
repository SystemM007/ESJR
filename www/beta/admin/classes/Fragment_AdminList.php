<?php

class Fragment_AdminList extends Fragment_Abstract
{
	protected $data;
	protected $headers;
	protected $clickData;
	protected $noClickColumns = array();
	protected $columnWidth = array();
	
	protected $sortable;
	protected $id;
	
	private $idField;
	private $coresEnabledField;
	
	public function __construct(Matrix $data, $idField, $coresEnabledField, $id = "list", array $headers = array())
	{
		$this->data = $data;
		$this->clickData = array_fill_keys($this->data->getRowHeaders(), NULL) ;
		
		if($idField) $this->setIdField($idField);
		if($coresEnabledField) $this->setCoresEnabledField($coresEnabledField);
		
		if($headers) $this->setHeaders($headers);
		
		$this->id = $id;
	}

	public function setIdField($field)
	{
		if(!$this->data->isColumn($field)) throw new Exception("Gegeven idField '$field' niet gevonden in data");
		$this->idField = $field;
	}
	
	public function setCoresEnabledField($field)
	{
		if(!$this->data->isColumn($field)) throw new Exception("Gegeven coresEnabled '$field' niet gevonden in data");
		$this->coresEnabledField = $field;
	}
	
	public function setHeaders($headers)
	{
		if($this->idField) $headers = array_merge(array($this->idField => NULL), $headers);
		if($this->coresEnabledField) $headers = array_merge(array($this->coresEnabledField => NULL), $headers);
		
		if($keys = $this->data->columnsDiff($headers, true)){
			throw new Exception("Geen overeenkomst in de columns '$keys' bij Headers: " . print_r($headers, true) . $this->data);
		}
		
		$this->headers = $headers;
	}
	
	public function addDeleteField(Core $Core, $objectName = "dit item", array $rows = NULL, $field = "deleteField")
	{
		if(!isset($rows)) $rows = $this->data->getRowHeaders();
		if(!is_array($rows)) $rows = array($rows);
		
		if(!$this->idField) throw new Exception("addDeleteField kan niet worden aangeroepen als er geen idField gegeven is");
				
		$this->data->addColumns($field);
		$this->headers = array_merge($this->headers, array("$field" => ""));
		$this->addNoClickColumn($field);
		$this->addColumnWidth($field, "50");
				
		foreach($rows as $rowId)
		{	
			$deleteID = $this->data->getValue($rowId, $this->idField);
			
			$Button = (string) new Fragment_Button_Delete($Core, $objectName, $deleteID, $options);
			$this->data->update($Button, $field, $rowId);
		}
	}
	
	public function addNoClickColumn($field)
	{
		$this->noClickColumns[] = $field;
	}
	
	public function addColumnWidth($field, $width)
	{
		$this->columnWidth[$field] = $width;
	}
	
	public function onClickAction($lifeId, $action, $data = array(), $options = array(), $confirm = NULL, $columns = NULL, $rows = NULL)
	{
		if(!isset($rows)) $rows = $this->data->getRowHeaders();
		if(!is_array($rows)) $rows = array($rows);
		
		foreach($rows as $rowId)
		{
			$rowData = $data;
		
			if($this->idField) $rowData = array_merge(array("id" => $this->data->getValue($rowId, $this->idField)), $rowData);
			
			$actionStr = new Fragment_JS_Action($lifeId, $action, $rowData, $options, $confirm);
			
			$this->clickData[$row] = $actionStr;
		}
	}
	
	/*public function onClickPage($pageName, $data = array(), $options = array(), $confirm = NULL, $columns = NULL, $rows = NULL)
	{
		if(!$rows) $rows = $this->data->getRowHeaders();
		if(!is_array($rows)) $rows = array($rows);
		
		foreach($rows as $row)
		{
			$rowData = $data;
		
			if($this->idField) $rowData = array_merge(array("id" => $this->data->getValue($row, $this->idField)), $rowData);
			
			$actionStr = new Fragment_JS_Page($pageName, $rowData, $options, $confirm);
			
			$this->clickData[$row] = $actionStr;
		}
	}*/
	
	public function onClickCore($data = array(), $options = array(), $confirm = NULL, $columns = NULL, $rows = NULL)
	{
		if(!isset($rows)) $rows = $this->data->getRowHeaders();
		if(!is_array($rows)) $rows = array($rows);
		
		if(!$this->idField) throw new Exception("onClickCore kan niet worden aangeroepen zonder een idField");
		
		foreach($rows as $row)
		{
			$ID = $this->data->getValue($row, $this->idField);
					
			$coreStr = new Fragment_JS_Core($ID, $data, $options, $confirm);
			
			$this->clickData[$row] = $coreStr;
		}
	}
	
	public function sortable($id, $set = true)
	{
		$this->sortable = $set;	
		$this->id = $id;
	}
		
	public function create()
	{
		$html = "";
		
		$html .= "<table cellpadding='0' cellspacing='0' class='";
		$html .= "listTable ";
		if($this->sortable)	$html .= "sortable ";
		$html .= "' >";
		$html .= "<thead>";
		
		if($this->headers)
		{
			$html .= "<tr>";
			
			if($this->sortable)
			{
				$html .= "<th class='pin'></th>";
			}
			
			foreach($this->headers as $columnId => $headerName)
			{
				if($columnId == $this->idField) continue;
				if($columnId == $this->coresEnabledField) continue;
				
				$html .= "<th ";
				if(isset($this->columnWidth[$columnId]))
				{
					$html .= "width='". $this->columnWidth[$columnId] ."' ";
				}
				$html .= ">";
				$html .= $headerName;
				$html .= "</th>";
			}

			if($this->coresEnabledField) $html .= "<th></th>";
			
			$html .= "</tr>";
		}
		$html .= "</thead>";
		$html .= "<tbody id='". $this->id ."'>";
		
		// kolomen in volgorde van headers, indien gegeven
		$columns = $this->headers ? array_keys($this->headers) : $this->data->getColumnHeaders();
		
		foreach($this->data->getRowHeaders() as $rowId)
		{
			$row = $this->data->getRow($rowId);

			$html .= "<tr class='blueHover' ";
			$html .= "id = 'adminListRow_" . $row[$this->idField] . "' ";
			$html .= ">";
				
			if($this->sortable)
			{
				$html .= "<td class='pin'></td>";
			}
			
			foreach($columns as $columnId)
			{
			
				if($columnId == $this->idField) continue;
				if($columnId == $this->coresEnabledField) continue;
				
				$html .= "<td ";
				
				if(! in_array($columnId, $this->noClickColumns) && $onclick = $this->clickData[$rowId])
				{
					//$html .= "onclick='$onclick' class='clickableCell' ";
					$html .= "class='clickableCell' ";
				}
				if(isset($this->columnWidth[$columnId]))
				{
					$html .= "width='". $this->columnWidth[$columnId] ."' ";
				}

				
				$html .= ">";
				$html .= $this->data->getValue($rowId, $columnId) ; 
				$html .= "</td>";
			}
			
			if($this->coresEnabledField)
			{
				$html .= "<td class='" . ($row[$this->coresEnabledField] ? "enabled " : "disabled") . "'></td>";
			}
			
			$html .= "</tr>";
		}
		
		$html .= "</tbody>";
		$html .= "</table>";
		
		return $html;
	}
}