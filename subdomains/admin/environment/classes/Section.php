<?php

class Section
{
	public static $cache = array();
	protected $data = array();
	
	
	/*
	 * construct wordt nog ondersteunt, maar afgeraden. Gebruik een getInstance!!
	 */
	public function __construct($ID)
	{
		$ID =  (int) $ID;
		
		
			/*
			 * dit is niet meer nodig als construct protected wordt gemaakt
			 */
			if(isset(self::$cache[$ID]))
			{
				
				$this->data = self::$cache[$ID]->getData();
			}
			else
			{
				$this->data =  MySql::selectRow("SELECT ID, urlName, parent FROM u_sections LEFT JOIN u_cores USING(ID) WHERE ID = '$ID'");
				if(!$this->data) throw new Exception("Section met ID '$ID' niet gevonden");
			}
			/*
			 * eind niet meer nodig
			 */
		
		// hoe dit op te lossen?! Numeric keys are renumbered!
		//self::$cache = array_merge(self::$cache, array($ID => $this));
		self::$cache[$ID] = $this;
		
	}
	
	public static function getInstance($ID)
	{
		if(isset(self::$cache[$ID])) return self::$cache[$ID];
		else return new self($ID);
	}
	
	
	public function getFullPath()
	{
		if($this->urlName == "")
		{
			return "/";
		}
		else
		{
			return $this->getParent()->getFullPath() . $this->data["urlName"] . "/";
		}
	}
	
	public function getParent()
	{
		return self::getInstance($this->data["parent"]);
	}
		
	public function getData()
	{
		return $this->data;
	}
	
	
	
	public function __get($name)
	{
		if(isset($this->data[$name]))
		{
			return $this->data[$name];
		}
		else
		{
			throw new Exception("Tried to access unknown property '$name'");
		}
	}
	
	public function __isset($name)
	{
		return isset($this->data[$name]);
	}
}