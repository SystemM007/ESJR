<?php

class Life
{
	public static function register($object)
	{
	    if (!isset(Session::$instance["life"])) {
            Session::$instance["life"] = array();
        }
		$lifeId = count(Session::$instance["life"]);
		Session::$instance["life"][$lifeId] = $object;
		return $lifeId;
	}

	public static function killAll()
	{
		unset(Session::$instance["life"]);
		Session::$instance["life"] = array();
	}
	
	public static function get($lifeId)
	{
		$life = Session::$instance["life"][$lifeId];
		
		if(!isset($life)) throw new Exception("Er is gevraagd naar lifeId '$lifeId', dit werd niet gevonden in de huidige populatie");
		
		if( !($life  instanceof Module_Life) ) throw new Exception("Object op lifeId '$lifeId' was geen instante van 'AdminModule' maar was een object van het type '". get_class($life) . "'.");
		
		return $life;
	}
}
