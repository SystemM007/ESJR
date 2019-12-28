<?php

abstract class Module_Split_Children extends Module_Abstract_Children implements Module_Basic_Children_Interface
{
	protected $classNames = array();
	
	private $Objects = array();
	
	public function __get($name)
	{
		if(isset($this->Objects[$name])) return $this->Objects[$name];
		throw new Exception("Can not get $name from " . print_r($this->Objects, true));
	}
	
	public function getChildClasses()
	{
		return $this->classNames;
	}
		
	protected function afterConstruct()
	{
		foreach($this->classNames as $objectName => $className)
		{
			/*
			 * De key van this->classNames wordt gebruikt als objectName
			 * Voor terugwaardse compabiliteit wordt indien het geen assiosiative array is de classname als objectname gebruikt
			 */
			if(is_numeric($objectName))
			{
				unset($this->className[$objectName]);
				$objectName = $className; 
				$this->className[$objectName] = $className;
			}
			/*
			 * Objecten mogen zonder underscore gegeven worden, dan wordt aangenomen dat het in de namespace van de huidige class staat
			 * Kortom:
			 * Wordt 'Images' gegeven door Module_MyPage_Children
			 * dan wordt gezocht naar Module_MyPage_Children_Images
			 * 
			 * DEPRECIATED! 
			 * Want zo wordt extenden ontzettend vervelend!
			 */
			if(!strpos($className, "_")) $this->classNames[$objectName] = get_class($this) . "_$className";
		}
		
		foreach($this->classNames as $objectName => $className)
		{
			$this->Objects[$objectName] = new $className($this->Core);
		}
	}
	
	/*
	 * Vereist door abstractie
	 */
	public function childTypeIsAllowed($module)
	{
		foreach($this->Objects as $Object)
		{
			if($Object->childTypeIsAllowed($module)) return true;
		}
		
		return false;
	}
	
	/*
	 * functie is vereist door inteface 
	 */
	public function getTable()
	{
		throw new Exception("getTable dient niet te worden gebruikt");
	}
	
	public function getButtons()
	{
		$return = "";
		foreach($this->Objects as $Object) $return .= $Object->getButtons(); 
		return $return;
	}
	
	public function getCreateInput()
	{
	}
	
	public function refresh()
	{
		foreach($this->Objects as $Object) $Object->refresh();
	}
}