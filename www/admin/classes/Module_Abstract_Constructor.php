<?php

abstract class Module_Abstract_Constructor extends Module_Core
{
	public function isRequestable($function)
	{
		return false;
	}
	
	final public function __construct($Core = NULL)
	{
		if($Core) parent::__construct($Core);
	}
	
	/*
	* @return int nieuwe leesniveau voor Core
	*/
	abstract protected function getNewReadLevel();
	
	/*
	* @return int nieuwe schrijfniveau voor Core
	*/
	abstract protected function getNewWriteLevel();
	
	/*
	* wordt aangeroepen voor wegschrijven van Core
	* @param : alle parameters die naar de Create functie worden gespeeld worden ook doorgespeeld (OOK DE PARENT!)
	* @return string nieuwe naam voor Core
	*/
	abstract protected function getNewCoreName();
	
	/*
	* @return bool instelling kinderen toegestaan voor Core
	*/
	abstract protected function getNewChildrenAllowed();
	
	/*
	* @return int nieuwe niveau kinderen aanmaken voor Core
	*/
	abstract protected function getNewChildCreateLevel();
	
	/*
	* @return bool nieuwe staat van enabled
	*/
	abstract protected function getNewEnabled();
	
	/*
	* wordt aangeroepen voor wegschrijven van Core
	* @param : alle parameters die naar de Create functie worden gespeeld worden ook doorgespeeld (OOK DE PARENT!)
	* @return void
	*/
	abstract protected function beforeCreate();
	
	/*
	* wordt aangeroepen na wegschrijven van Core
	* @param : alle rest parameters die naar de Create functie worden gespeeld worden ook doorgespeeld (NIET DE PARENT!)
	* @return void
	*/
	abstract protected function onCreate();
	

	/*
	* wordt aangeroepen voor verwijderen van Core (mogelijkheid tot opruimen kinderen!)
	* @return void
	* @todo Generale functie maken om bij verwijderen automatisch kinderen te verwijderen.
	*/
	abstract protected function beforeDelete();

	/*
	* wordt aangeroepen na verwijderen van Core
	* @return void
	*/
	abstract protected function onDelete();
		
	/*
	* hiermee wordt een Core aangemaakt
	* @param Core $Parent is een Core object van de Core waaronder deze Core moet komen te liggen
	* @param rest: sommige Modulen hebben meer parameters nodig. Deze worden doorspeeld naar de functies beforeCreate en onCreate
	* @return void
	*/
	final public function create(Core $Parent)
	{
		if($this->Core) throw new Exception("Kan geen create uitvoeren op bestaande Core!");
		
		$arguments = func_get_args();
		call_user_func_array(array($this, "beforeCreate"), $arguments);
	
		$module = $this->getModuleName();
		
		$constructLevel = MySql::selectValue(array(
			"select" => "constructLevel",
			"from" => "u_modules",
			"where" => "`module` = '$module'",
		));
		
		if($constructLevel === NULL)
		{
			throw new Exception("Kon de module '$module' niet vinden.");
		}
		
		if(!User::levelAllowed($constructLevel))
		{
			throw new Exception("De gebruiker met level ". User::getLevelName() . " heeft geen constructie rechten voor de module '$module' met level " . User::getAccessLevelName($constructLevel));
		}
		
		// parent checken
		if(!$Parent->childrenAllowed) throw new Exception("Het is niet mogelijk om kinderen aan te maken onder een '". $Parent->module ."'");
		$Parent->checkChildCreateAccess("Creatie nieuw kind van module' $module'");
		if(!$Parent->Children->childTypeIsAllowed($module)) throw new Exception("De module '". $Parent->module ."' staat geen kinderen toe van het type '$module'");
			
		$newReadLevel = $this->getNewReadLevel();
		$newReadLevel = isset($newReadLevel) ? $newReadLevel : $constructLevel;
		
		$newWriteLevel = $this->getNewWriteLevel();
		$newWriteLevel = isset($newWriteLevel) ? $newWriteLevel : $constructLevel;
		
		$childrenAllowed = $this->getNewChildrenAllowed();
		
		if($childrenAllowed)
		{
			$newChildCreateLevel = $this->getNewChildCreateLevel();
			$newChildCreateLevel = isset($newChildCreateLevel) ? $newChildCreateLevel : $constructLevel;
		}
		else
		{
			$newChildCreateLevel = User::ACCESSLEVEL_EASY;
		}
		
		$newEnabled = $this->getNewEnabled();
		
		$ID = MySql::insert(array(
			"table" => "u_cores",
			"values" => array(
				"name" => call_user_func_array(array($this, "getNewCoreName"), $arguments),
				"module" => $module,
				"readLevel" => $newReadLevel,
				"writeLevel" => $newWriteLevel,
				"childCreateLevel" => $newChildCreateLevel,
				"parent" => $Parent->ID,
				"childrenAllowed" => $childrenAllowed ? "1" : "0",
				"childrenAllowed" => $childrenAllowed ? "1" : "0",
				"enabled" => $newEnabled ? "1" : "0",
			)
		));
		
		$this->__construct(new Core($ID));
		
		array_shift($arguments);
		call_user_func_array(array($this, "onCreate"), $arguments);
		
		return $this->Core;
	}
	
	/*
	* hiermee wordt een Core verwijderd
	* @return void
	*/
	final public function delete()
	{
		if(!$this->Core) throw new Exception("Delete kan niet worden aangeroepen als er geen Core is geset");
	
		$this->beforeDelete();
		
		$this->Core->checkConstructAccess("Verwijderen van Core '". $this->Core->ID ."' van het type '". $this->Core->module ."', construct access check");
		$this->Core->ParentCore->checkChildCreateAccess("Verwijderen kind '". $this->Core->ID ."' van het type '". $this->Core->module ."', childCreate access check");
	
		$n = MySql::numRowsSelect(array(
			"from" => "u_cores",
			"where" => "parent = '". $this->Core->ID ."'"
		));
		if($n) 
		{
			Response::msg("Kan afdeling niet verwijderen omdat deze niet leeg is!");
			return false;
		}
		
		$this->onDelete();
	
		MySql::delete(array(
			"table" => "u_cores",
			"where" => "`ID` = '". $this->Core->ID . "'",
			"limit" => "1",
		));
	}
}