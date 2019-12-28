<?php

abstract class Module_Life
{
	private $lifeId;
	
	/*
	* registreert het huidige object in het Life register
	* @return void
	*/
	final protected function registerLife()
	{
		$this->lifeId = Life::register($this);
	}
	
	/*
	* @return int lifeId, de unieke nummer van dit object in het register van objecten die bewaard moeten worden in de sessie
	*/
	final public function getLifeId()
	{
		if(!isset($this->lifeId)) throw new Exception("Life Id gevraagd waar deze niet geregisteerd is!");
		return $this->lifeId;
	}
	
	/*
	* geeft aan of een bepaalde (public) functie ervoor gemaakt is om te worden aangeroepen als actie
	* @param string $module Naam van de module
	* @return bool Welke aangeeft of de module onder deze Core toegestaan (true) of niet toegestaan (false) is.
	*/
	abstract function isRequestable($function);
}
