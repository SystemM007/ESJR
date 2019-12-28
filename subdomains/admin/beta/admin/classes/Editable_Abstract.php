<?php

abstract class Editable_Abstract
{
	private static $editableIds = 0;


	private $editableId;
	private $name;
	
	protected $options = array();
	protected $check = array();
	
	protected $table;
	protected $idField;
	protected $dataId;
	protected $field;
	protected $conditions;
	protected $dataFieldSet = false;
	
	protected $callBack = array();

	protected $useValue;

	public function __construct($editType, $name, array $dataFieldArray = array())
	{
		$this->options["editType"] = $editType;

		$this->setName($name);
		
		$this->editableId = preg_replace("/[^a-z]/", "", strtolower($this->name)) . "_" . self::$editableIds++;
		
		if($dataFieldArray) call_user_func_array(array($this, "setDataField"), $dataFieldArray);
		
		$this->onConstruct();
	}
	
	protected function onConstruct(){} // kan fijn op doorgeborduurd worden, zonder elke keer een construct over te nemen

	public function setName($name)
	{
		$this->name = htmlspecialchars($name);
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function setDataField($table, $idField, $dataId, $field, $conditions = NULL)
	{
		if(!$table) throw new Exception("table moet worden gegeven", E_USER_ERROR);
		if(!$idField) throw new Exception("idField moet worden gegeven", E_USER_ERROR);
		if(!$dataId) throw new Exception("dataId moet worden gegeven", E_USER_ERROR);
		if(!$field) throw new Exception("field moet worden gegeven", E_USER_ERROR);
		
		$this->table = $table;
		$this->idField = $idField;
		$this->dataId = $dataId;
		$this->field = $field;
		$this->conditions = $conditions;
		
		$this->dataFieldSet = true;
	}
	
	public function checkMustBeUnique($set = true)
	{
		if(!$this->dataFieldSet) throw new Exception("Optie Must be Unique kan niet worden gebruikt als er geen datafield is geset", E_USER_ERROR);
		$this->check["mustBeUnique"] = $set;
	}
	
	public function checkNonEmpty($set = true)
	{
		$this->check["nonEmpty"] = $set;
	}
	
	public function checkMaxLength($maxLength)
	{
		$this->check["maxLength"] = $maxLength;
	}
	
	public function optionDirectConvert($set = true)
	{
		$this->options["directConvert"] = $set;
	}
	
	public function optionGiveFocus($set = true)
	{
		$this->optionDirectConvert(!$set);
		$this->options["giveFocus"] = $set;
	}
	
	public function setValue($value)
	{
		$this->useValue = $value;
	}

	public function addCallBack($callBack)
	{
		if(!is_callable($callBack))
		{
			throw new Exception("Callback is niet callable");
		}
		
		$this->callBack[] = $callBack;
	}

	public function makeEditable()
	{
		$html = "";
		$html .= new Fragment_Tag_Basic("h2", false, array("class"=>"editableHeader"), $this->name);
		$html .=  $this->createHTML();
		
		Response::editable($this->editableId, $this->options);
		
		return $html;
	}
	
	final public function __toString()
	{
		try
		{
			$str = (string) $this->makeEditable();
		}
		catch(Exception $e)
		{
			trigger_error("Fout in de __toString method van " . get_class($this) ." object: " . $e->getMessage(), E_USER_ERROR);
		}
		
		return $str;
	}
	
	public function getEditableId()
	{
		return $this->editableId;
	}
		
	public function getInput()
	{
		// controleren of er relevante input is
		if(!isset(Request::$Input[$this->editableId]))
		{
			return ;
		}
		
		$value = Request::$Input[$this->editableId];

		try
		{
			$value = $this->rewriteInput($value);
			$this->checkInput($value);
			$this->generalChecks($value);
			$value = $this->rewriteInputPost($value);
		}
		
		catch(Editable_Exception $Exception)
		{
			$this->feedbackError();		
			// rethrow
			throw $Exception;
		}
		
		return $value;
	}
	
	public function save()
	{
		if(!$this->dataFieldSet) throw new Exception("save kan niet worden gebruikt als er geen datafield is geset");
		
		$value = $this->getInput();
		if(!isset($value)) return false;

		$conditions = $this->conditions ? " AND $this->conditions" : "";
		MySql::update(array(
			"values" => array($this->field => $value),
			"table" => $this->table,
			"where" => "$this->idField = '$this->dataId'" . $conditions
		));
		
		$this->feedbackSaved();
		
		foreach($this->callBack as $callBack)
		{
			call_user_func($callBack, array("value" => $value, "input" => Request::$Input[$this->editableId]));
		}
		
		return true;
	}
	
	public function feedbackSaved()
	{
		Response::feedback($this->editableId, "saved");
	}
	
	public function feedbackError()
	{
		Response::feedback($this->editableId, "error");
	}
	
	protected function getValue()
	{
		if($this->useValue) return $this->useValue;
		
		if($this->dataFieldSet)
		{
			$conditions = $this->conditions ? " AND $this->conditions" : "";
			return MySql::selectValue(array(
				 "select" => $this->field,
				 "from" => $this->table,
				 "where" => "$this->idField = '$this->dataId'" . $conditions
				));
		}
		
		return "";
	}
	
	protected function createHTML()
	{
		return new Fragment_Tag_Div($this->editableId, array(), (string) new Fragment_Tag_Div("", array(), $this->getValue()));
	}
	
	protected function checkInput($value)
	{
	}
	
	protected function rewriteInput($value)
	{
		return $value;
	}
	
	protected function rewriteInputPost($value)
	{
		return $value;
	}
	
	private function generalChecks($value)
	{
		if($this->check["nonEmpty"])
		{
			if(!strlen($value)) throw new Editable_Exception("Dit veld mag niet leeg zijn");
		}
	
		if($this->check["maxLength"])
		{
			if(strlen($maxLength) > $this->check["maxLength"]) throw new Editable_Exception("Dit veld mag niet meer dan");
		}
		
		if($this->check["mustBeUnique"])
		{
			$conditions = $this->conditions ? " AND $this->conditions" : "";
			$n = MySql::numRowsSelect(array(
				 "from" => $this->table,
				 "where" => "$this->field = '$value' AND $this->idField != '$this->dataId'" . $conditions
			));
			
			if($n) throw new Editable_Exception("Deze waarde moet uniek zijn maar komt al voor!");
		}
	}
}
	
	