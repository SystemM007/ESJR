<?php

class DataSet extends Matrix{

	public function __construct($data = false, $columns = false, $assoc = false)
	{
		parent::__construct($data, $columns, $assoc);
	}
	

	public function makeListable()
	{
		$args = func_get_args();
		call_user_func_array(array($this, "htmlspecialchars"), $args);
		call_user_func_array(array($this, "nbspForEmpty"), $args);
		call_user_func_array(array($this, "trunicate"), $args);/* strip tags zit ingebouwd */
	}

	
	public function nbspForEmpty()
	{
		$this->invoke(func_get_args(), array("StringFunctions", "nbspForEmpty"));
	}
			
	public function stripTags()
	{
		$this->invoke(func_get_args(), array("StringFunctions", "stripTags"));
	}
	
	public function stripTagsExcept()
	{
		$args = func_get_args();
		$allowedTags = array_shift($args);
		$this->invoke($args, array("StringFunctions", "stripTags"), $allowedTags);
	}
	
	public function trunicate()
	{
		$this->invoke(func_get_args(), array("StringFunctions", "trunicate"));	
	}
	
	public function date()
	{
		$this->invoke(func_get_args(), array("StringFunctions", "date"));
	}
	
	public function nl2br()
	{
		$this->invoke(func_get_args(), "nl2br");
	}
	
	public function htmlentities()
	{
		$this->invoke(func_get_args(), "htmlentities");	
	}
	
	public function htmlspecialchars()
	{
		$this->invoke(func_get_args(), "htmlspecialchars");	
	}
	
	public function html_entity_decode()
	{
		$this->invoke(func_get_args(), "html_entity_decode");
	}
	
	public function datef()
	{
		$args = func_get_args();
		$format = array_shift($args);
		$this->invoke($args, array("StringFunctions", "date"), $format);
	}
	
	public function wrap()
	{
		$args = func_get_args();
		$prefix = array_shift($args);
		$suffix = array_shift($args);
		
		$this->invoke($args, array("StringFunctions", "wrap"), $prefix, $suffix);
	}
	
	public function addDecimals()
	{
		$args = func_get_args();
		$num = array_shift($args);
		$this->invoke($args, array("StringFunctions", "sprintf"), "%01." .$num ."f");
	}

	
	public function invoke(array $fields, $function, $restArgs = array())
	{
		$args = func_get_args();
		$fields = array_shift($args);
		$function = array_shift($args);			

		if(!$fields) $fields = $this->columns;
		if(!$function) throw new Exception("Fout in parameters: function niet gegeven", E_USER_ERROR);
	
		for($i = 0; $i < count($this->items); $i++)
		{
			foreach($fields as $field)
			{
				$funcArgs = array_merge( array($this->items[$i][$field]), $args);
				$this->items[$i][$field] = call_user_func_array($function, $funcArgs);
			}
		}
	}
	
	public function addImgColumn($srcColumn, $srcPrefix = "", $srcPostfix = "", $altColumn = NULL, $imgColumn = "img")
	{
		if(!$this->isColumn($srcColumn)) throw new Exception("src kolom '$srcColumn' werd niet gevonden");
		if(isset($altColumn) && !$this->isColumn($altColumn)) throw new Exception("alt kolom '$altColumn' werd niet gevonden");
		
		if(!$this->isColumn($imgColumn)) $this->addColumns($imgColumn);
				
		foreach($this as $rowId => $row)
		{
			$alt = $altColumn ? $row[$altColumn] : "";
			$img = "<img src='" . $srcPrefix . $row[$srcColumn] . $srcPostfix . "' alt='$alt' />";
			$this->update($img, $imgColumn, $rowId);
		}
	}
	
	public function html2plain($fields = NULL, $width = NULL, $allowedTags = NULL, $baseUrl = NULL)
	{
		if(!isset($fields)) $fields = $this->getColumnHeaders();
		if(!is_array($fields)) $fields = array($fields);
		
		$Html2plain = new Html2plain();
		
		if(isset($width)) $Html2plain->setWidth($width);
		if(isset($allowedTags)) $Html2plain->setAllowedTags($allowedTags);
		if(isset($baseUrl)) $Html2plain->setBaseUrl($baseUrl);
	
		
		foreach($fields as $field)
		{
			foreach($this as $rowId => $row)
			{
				if(!$this->isColumn($field)) throw new Exception("field '$field' niet gevonden in Dataset");
				
				$Html2plain->setHtml($row[$field]);
				
				$this->update((string) $Html2plain, $field, $rowId);
			}
		}
	}
	
}
	
	
