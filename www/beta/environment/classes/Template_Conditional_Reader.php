<?php

class Template_Conditional_Reader extends Template_Collector_Reader
{
	public function makeElement()
	{	
		$Element = new Template_Conditional_Element($this->elements);
		
		if(isset($this->attributes["keys"]))
		{
			$conditions = $this->makeConditions($this->attributes["keys"]);
			$Element->setConditions($conditions);
		}
		
		if(isset($this->attributes["firstfill"]))
		{
			$Element->isFirstFill( $this->getBoolAttribute("firstfill") );
		}
	
		if(isset($this->attributes["lastfill"]))
		{
			$Element->isLastFill( $this->getBoolAttribute("lastfill") );
		}
		
		return $Element;
	}
	
	private function makeConditions($keys)
	{
		$keys = explode(" ", $this->attributes["keys"]);
		
		$conditions = array();
		foreach($keys as $key)
		{
			if(substr($key, 0, 1) == "!")
			{
				$key = substr($key, 1);
				$conditions[$key] = false;
			}
			else
			{
				$conditions[$key] = true;
			}
		}
		
		return $conditions;
	}
	
	private function getBoolAttribute($attribute)
	{
		switch($this->attributes[$attribute])
		{
			case "true" : return true;
			case "false" : return false;
			default : throw new Template_Exception("Een bool attribuut mag enkel 'true' of 'false' bevatten, geen '$value'", $this);
		}
	}
}