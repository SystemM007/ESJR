<?php

class Matrix implements Iterator {

	protected $items = array();
	protected $columns = array();
	protected $assoc = false;
	
	public function __construct($data = false, $columns = false, $assoc = false)
	{
		if($columns)
		{
			$this->setColumns($columns);
		}
		elseif(is_array($data) && $data[0])
		{
			$this->setColumns(array_keys($data[0]));
		}
		
		if($data)
		{
			if(!is_array($data)) throw new Exception("Data moet een array zijn");
			$this->addRows($data);
		}
		$this->assoc = (bool) $assoc;
	}
	
	public function rewind() 
	{
		return reset($this->items);
	}
	
	public function current()
	{
		return current($this->items);
	}
	
	public function key()
	{
		return key($this->items);
	}
	
	public function next()
	{
		return next($this->items);
	}
	
	public function valid()
	{
		return ($this->current() !== false);
	}
	
	public function ___toString($html = false)
	{
		$r = "";
		
		if($html)
		{
			$r .= "<table>";
			$r .= "<tr>";
			$r .= "<th>Matrix</th>";
			foreach($this->columns as $column)	$r .= "<th>$column</th>";
			$r .= "</tr>";
			
			foreach($this->items as $row => $item)
			{
				$r .= "<tr>";
				$r .= "<th>$row</th>";
				$r .= "<td>" . implode("</td><td>", $item) ."</td>";	
				$r .= "</tr>";
			}
			$r .= "</table>";
		}
		else{
			$r .= "Matrix";
			foreach($this->columns as $column)	$r .= "\t\t$column";
			$r .= "\n";
			
			foreach($this->items as $row => $item)
			{
				$r .= "$row";
				$r .= "\t\t" . implode("\t\t", $item);	
			}
		}
		
		return $r;
		
		
	}
	
	public function toArray()
	{
		return $this->items;
	}
	
	public function isRow($row)
	{
		return isset($this->items[$row]);
	}
	
	public function isColumn($column)
	{
		return in_array($column, $this->columns);
	}
	
	public function columnsDiff(array $rowData, $e = false)
	{
		
		$d1 = array_diff(array_keys($rowData), $this->columns);
		$d2 = array_diff($this->columns, array_keys($rowData));
		$cols = array_merge($d1, $d2);
		
		if($cols)
		{
			return "'" . implode("', '", $cols) . "'";
		}
		else{
			return false;
		}
	}
	
	public function rowCountFit($data)
	{
		return count($data) == count($this->items);
	}

	public function count()
	{
		return count($this->items);
	}
	
	public function getRows()
	{
		throw new Exception("Depriciated: getRows. Is verwarrend, dus gebruik 'getRowHeaders'");
	}
	
	public function getRowHeaders()
	{
		return array_keys($this->items);
	}
	
	public function getColumns()
	{
			throw new Exception("Depriciated: getColumns. Is verwarrend, dus gebruik 'getColumnHeaders'");
	}
	
	public function getColumnHeaders()
	{	
		return $this->columns;
	}
	
	public function getData($rows = NULL, $columns = NULL)
	{
		if(!isset($columns)) $columns = $this->columns;
		if(!is_array($columns)) $columns = array($columns);
		
		if(!isset($rows)) $rows = array_keys($this->items);
		if(!is_array($rows)) $rows = array($rows);
		
		$return = array();
		
		foreach($rows as $rowId)
		{
			
			if(!$this->isRow($rowId)) throw new Exception("Er is gevraagd naar rij '$rowId', deze rij bestaat niet in de Dataset.");
			
			foreach($columns as $columnId)
			{
				if(!$this->isColumn($columnId)) throw new Exception("Er is gevraagd naar kolom '$columnId', deze key bestaat niet in de Dataset.");
				$return[$rowId][$columnId] = $this->items[$rowId][$columnId];
			}
			
		}
		return $return;
	}
	
	public function getValue($rowId, $columnId)
	{
		$r = $this->getData($rowId, $columnId);		
		return $r[$rowId][$columnId];
	}
	
	public function getRow($rowId, $columns = NULL)
	{
		$r = $this->getData($rowId, $columns);		
		return $r[$rowId];
	}
	
	// attentie!!! columns en rows zijn hier omgekeerd
	public function getColumn($columnId, $rows = NULL)
	{
		$data = $this->getData($rows, $columnId);		
		
		$return = array();
		
		foreach($data as $rowId => $rowData) $return[$rowId] = $rowData[$columnId];
		
		return $return;
	}
	
	public function setValue($rowId, $columnId)
	{
		if(!$this->isRow($rowId)) throw new Exception("Er is gevraagd naar rij '$rowId', deze rij bestaat niet in de Dataset.");	
		
	
	}
		
	public function setColumns(array $columns)
	{
		if($this->columns) trigger_error("setColumns is uitgevoerd terwijl de keys van de dataset al zijn gedefeni�erd");
		$this->columns = $columns;
	}
	
	public function addRows(array $rowDataArr)
	{
		foreach($rowDataArr as $rowName => $rowData)
		{
			if(! is_array($rowData)) throw new Exception("Rowdata is nog an array");
			$this->addRow($rowData, $rowName);
		}
	}
	
	public function addRow(array $rowData, $rowName = NULL)
	{
		if( ($col = $this->columnsDiff($rowData)) !== false)
			throw new Exception("Kolommen '$col' van ingevoegde rij kwamen niet overeen met die van de Matrix" . print_r($this->columns, true));
			
		if($this->assoc && is_string($rowName))
		{
			if(isset($this->items[$rowName])) trigger_error("Er is geprobeerd een rij toe te voegen op een bestaande rij '$column'");
			$this->items[$rowName] = $rowData;
			return $rowName;
		}
		else
		{
			$this->items[] = $rowData;
			return count($this->items) -1;
		}
	}
	
	public function addColumns($columns, $fillVar = NULL)
	{
		if(is_string($columns)) $columns = array($columns);
		
		foreach($this->items as $i => $item)
			$this->items[$i] = array_merge($this->items[$i], array_fill_keys($columns, $fillVar));
		
		$this->columns = array_merge($this->columns, $columns);
	}
	
	public function cloneColumn($columnName, $destinationColumn)
	{
		if(!$this->isColumn($columnName)) trigger_error("Geprobeerd te klonen van niet bestaande kolom '$columnName'", E_USER_ERROR);

		$this->addColumns($destinationColumn);
		
		foreach($this->items as $rowId => $rowData)
		{
			$this->items[$rowId][$destinationColumn] = $rowData[$columnName];
		}
		
	}
	
	public function update($val, $columns = NULL, $rows = NULL)
	{
		if(!isset($columns)) $columns = $this->columns;
		if(!is_array($columns)) $columns = array($columns);
		
		if(!isset($rows)) $rows = array_keys($this->items);
		if(!is_array($rows)) $rows = array($rows);
		
		
		foreach($rows as $row)
		{
			if(!$this->isRow($row)) throw new Exception("Er is geprobeerd de DataSet te updaten op de row '$row', deze bestaat echter niet in de DataSet");

			foreach($columns as $column) 
			{
				if(!$this->isColumn($column)) throw new Exception("Er is geprobeerd het kolom '$column' te updaten dat niet werd gevonden in de DataSet");
				$this->items[$row][$column] = $val;
			}
		}
	}
	
	public function unsetColumns($columns)
	{
		if(is_string($columns)) $columns = array($columns);
		
		foreach($columns as $columnId)
		{
			if(!$this->isColumn($columnId)) throw new Exception("Kolom '$columnId' kan niet worden geUnset, het bestaat niet in de matrix");
			unset($this->columns[array_search($columnId, $this->columns)]);
			
			foreach(array_keys($this->items) as $rowId) unset($this->items[$rowId][$columnId]);
		}	
		
	}
	
	public function emptyClone($fillVal = NULL)
	{
		$newData = clone $this;
		$newData->update($fillVar);
		return $newData;
	}
	
	public function selectEqual($column, $values, $strict = false)
	{	
		if(!$this->isColumn($column)) throw new Exception("Er is geprobeerd te zoeken in de Matrix op kolom '$column', welke niet bestaat.");

		if(!is_array($values)) $values = array($values);

		$result = new Matrix(false, $this->columns, $this->assoc);
		
		foreach($this as $rowId => $row)
		{
			if(in_array($row["$column"], $values, $strict))
			{
				$result->addRow($row, $rowId);
			}
		}
		
		return $result;
	}
}
	
	
