<?php

class Template_Conditional_Element extends Template_Collector_Element
{
	/*
	* Associative array met 
	* key is een key waarop gecontroleert wordt
	* value een bool die zegt of de locatie wel of niet moet zijn gevuld.
	*/
	protected $conditions = array();
	
	protected $isFirstFill;
	protected $isLastFill;
	
	public function setConditions(array $conditions)
	{
		$this->conditions = $conditions;
	}
	
	public function isFirstFill($firstFill)
	{
		$this->isFirstFill = (bool) $firstFill;
	}
	
	public function islastFill($lastFill)
	{
		$this->isLastFill = (bool) $lastFill;
	}

	public function content(array $fill, array $options)
	{
		if( 
			$this->checkConditions($fill, $options)
			&& $this->checkFirstFill($fill, $options)
			&& $this->checkLastFill($fill, $options)
		)
		{
			return parent::content($fill, $options);
		}
		else
		{
			return "";
		}
	}
	
	private function checkConditions(array $fill, array $options)
	{
		foreach($this->conditions as $key => $mustBeFilled)
		{
			//echo "\n$key:$mustBeFilled\t\t" . print_r($fill[$key], true);
			if( $this->toBool($fill[$key]) != $mustBeFilled) return false;
		}
		
		return true;
	}
	
	private function toBool($var)
	{
		if($var instanceof Matrix) return (bool) $var->count();
		else return (bool) $var;
	}
	
	private function checkFirstFill(array $fill, array $options)
	{
		return !isset($this->isFirstFill) || ($options["isFirstFill"] === $this->isFirstFill);
	}
	
	private function checkLastFill(array $fill, array $options)
	{
		return !isset($this->isLastFill) || ($options["isLastFill"] === $this->isLastFill);
	}
	
	public function tree($depth = 0)
	{
		$return = $this->objectAsTree($depth);
		foreach($this->conditions as $key => $set) $return .= "[$key:" . ($set ? "true" : "false") . "]";
		$return .= $this->elementsAsTree($depth);
		return $return;
	}
}