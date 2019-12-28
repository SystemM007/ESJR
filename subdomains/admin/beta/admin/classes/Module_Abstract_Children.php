<?php

abstract class Module_Abstract_Children extends Module_Core
{
	final public function __construct(Core $Core)
	{
		parent::__construct($Core);
		$this->Core->checkReadAccess("Opstarten Children");

		$this->registerLife($this);
		$this->afterConstruct();
	}
	
	final public function __wakeUp()
	{
		$this->Core->checkReadAccess("Opwekken Children");
	}
	
	/* 	
	* Bruikbaar voor allerlij doeleinden. 
	* B.V. Gebruikt om twee andere Children modules in deze module te hangen
	* in BCXShoutbox_Children
	* @return void 
	*/
	protected function afterConstruct()
	{
	}
	
	/*
	* geeft aan of de module $module onder de huidige Core is toegestaan
	* @param string $module Naam van de module
	* @return bool Welke aangeeft of de module onder deze Core toegestaan (true) of niet toegestaan (false) is.
	*/
	abstract public function childTypeIsAllowed($module);
	
	/*
	* Kan altijd worden aangeroepen om een reeds aangemaakte pagina een nieuwe versie children te geven
	* Merk op dat de create input standaard niet wordt herladen
	* de functie kan worden overschreven om andere / meer onderdelen van de pagina te verversen
	* @return void
	*/
	abstract public function refresh();
	

	/* 
	* REQUESTABLE
	* id : naam van de module
	* creeert nieuwe module (createModule) van type Request::$Post["id"]
	* en start dan de Page van die Core
	* @return void
	*/
	final public function create($createModule)
	{
		$constructor = "Module_". $createModule ."_Constructor";
		$ID = $this->getID();
		
		if(!class_exists($constructor)) throw new Exception("Classe '$constructor' bestaat niet!");
		if(!is_subclass_of($constructor, "Module_Abstract_Constructor")) throw new Exception("De constructor classe van gevraagde module '$createModule' is geen subclasse van Module_Abstract_Constructor");		

		$Constructor = new $constructor();
		$NewCore = $Constructor->create($this->Core);
		
		if($NewCore)
		{
			Response::evalJs(new Fragment_JS_Core($NewCore->ID));
		}
		else
		{
			return;
		}
	}
	
	/* 
	* SERVERACTION
	* id : ID van te verwijderen Core
	* verwijdert Core met ID Request::$Post["id"]
	* en ververst daarna de lijst
	* @return void
	* @todo DIT KAN LOS! Bijvoorbeeld zou dit naar Fragment_Button_Delete kunnnen, of een andere kleine "motor" klasse.
	* Wat daarvoor wel moet gebeuren is een soort "global" die aangeeft wat de huidige Core is, en daarmee de lijst kan verversen
	* In het kort: memo's zouden in het geheel niet nodig hoeven te zijn
	*/
	final public function delete($ID)
	{
		$DeleteCore = new Core($ID);
		
		$DeleteCore->Constructor->delete();
		
		$this->refresh();
	}
	
	/* 
	* REQUESTABLE
	* verwijdert ALLE CHILDREN onder huidige Core
	* ALLEEN TOEGANG VOOR ACCESSLEVEL_HIGHEST
	* en ververst daarna de lijst
	* @return void
	*/
	final public function deleteAllChildren()
	{
		if(!User::levelAllowed(User::ACCESSLEVEL_HIGHEST))
		{
			throw new Exception("Geen toegang");
		}
		else
		{
			new TotalSectionCleanup($this->Core);
			
			$this->refresh();	
		}
	}
	
	public function isRequestable($function)
	{
		return in_array($function, array("create", "deleteAllChildren")); //niet meer nodig door ServerAction: delete
	}
}

