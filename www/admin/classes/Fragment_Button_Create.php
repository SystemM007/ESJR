<?php

class Fragment_Button_Create extends Fragment_Button_ServerAction
{
	public function __construct($moduleName, $Core, $module)
	{
		// werkomheen
		if(is_numeric($Core)) $Children = Life::get($Core);
		// eigenlijk moet er een object van het type Core worden gegeven
		else
		{
			if(! $Core instanceof Core) throw new Exception("Second parameter has to be a Core object, a lifeId of a Children object is accepted to for the sake of backwards compability. " . print_r($Children, true));
			$Children = $Core->Children;
		}
		
		parent::__construct($moduleName, new ServerAction(array($Children, "create"), $module));
	}
}