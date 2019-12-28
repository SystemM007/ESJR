<?php
class Core
{
	protected $ID;
	protected $name;
	protected $module;
	protected $readLevel;
	protected $writeLevel;
	protected $childCreateLevel;
	protected $parent;
	protected $childrenAllowed;
	protected $constructLevel;
	protected $universal;
	protected $isSection;
	
	protected $Page;
	protected $Edit;
	protected $Constructor;
	protected $Children;
	
	protected $ParentCore;
	

	public function __construct($ID)
	{
		$this->getCoreData($ID);
	}
	
	public function refresh()
	{
		$this->getCoreData($this->ID);
	}
	
	private function getCoreData($ID)
	{
		$coreData = MySql::selectRow(array(
			"select" => array("u_cores.ID", "u_cores.name", "u_cores.module", "u_cores.readLevel", "u_cores.writeLevel", "u_cores.childCreateLevel", "u_cores.parent", "u_cores.childrenAllowed", "u_modules.constructLevel", "u_modules.universal", "u_modules.isSection"),
			"from" => "u_cores",
			"join" => array("table" => "u_modules", "using" => "module"),
			"where" => "u_cores.ID = '$ID'",
		));
		
		if(!count($coreData)) throw new Exception("Core '$ID' niet gevonden");
		
		foreach($coreData as $name => $value) $this->$name = $value;
	}
	
	public function __get($name)
	{
		if(!isset($this->$name))
		{
			$this->$name = $this->create($name);
		}
		return $this->$name;
	}
	
	private function create($name)
	{
		switch($name)
		{
			case "ParentCore" :
				return new Core($this->parent);
			break;
			
			case "Page" :
			case "Edit" :
			case "Constructor" :
			case "Children" :
				
				$class = "Module_".$this->module."_$name";
				$abstract = "Module_Abstract_$name";
				
				// Hele vreemde PHP Bug maakte dit nodig
				//Autoload::preloadClass($class);
				//Autoload::preloadClass($abstract);
				
				if(!class_exists($class))
				{
					throw new Exception("Class '$class' werd niet gevonden");
				}				
				
				if(! is_subclass_of($class, $abstract) )
				{
					throw new Exception("Module '$class' is geen subclasse van $abstract");
				}
				
				return new $class($this);
			break;
		}
		
		throw new Exception("Gevraagd naar $name, wat geen door Core aan te maken eigenschap is!");
	}
	
	public function readAccess($userLevel = NULL)
	{
		return User::levelAllowed($this->readLevel, $userLevel);
	}
	
	public function writeAccess($userLevel = NULL)
	{
		return User::levelAllowed($this->writeLevel, $userLevel);
	}
	
	public function constructAccess($userLevel = NULL)
	{
		return User::levelAllowed($this->constructLevel, $userLevel);
	}
	
	public function childCreateAccess($userLevel = NULL)
	{
		return User::levelAllowed($this->childCreateLevel, $userLevel);
	}
	
	// check
	public function checkReadAccess($context, $userLevel = NULL)
	{
		$this->checkAccess("readLevel", $context, $userLevel);
	}

	public function checkWriteAccess($context, $userLevel = NULL)
	{	
		$this->checkAccess("writeLevel", $context, $userLevel);
	}

	public function checkConstructAccess($context, $userLevel = NULL)
	{
		$this->checkAccess("constructLevel", $context, $userLevel);
	}
	
	public function checkChildCreateAccess($context, $userLevel = NULL)
	{
		$this->checkAccess("childCreateLevel", $context, $userLevel);
	}
	
	protected function checkAccess($levelName, $context, $userLevel = NULL)
	{
		if(! User::levelAllowed($this->$levelName, $userLevel))
		{
			throw new Access_Exception($context, $this->$levelName, $userLevel, $this->ID);
		}
	}
	
}