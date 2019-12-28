<?php

Session::init($_POST["instanceId"]);

$type = array_shift(Request::$Path);

try
{
	switch($type)
	{	
		case "upload" :
			
			Response::isUpload();
			if(!Request::$File)
			{
				Response::msg("Geen bestand ontvangen, mogelijk een te groot bestand verstuurd" . print_r(Request::$File, true));
				break;	
			}
			// do NOT break
			
		case "serveraction" :
			
			$ServerAction = Life::get((int) $_POST["lifeId"]);
			if(! $ServerAction instanceof ServerAction ) throw new Exception("Object on posted lifeid is not an ServerAction");
			else $ServerAction->call();
			
		break;
		
		
		case "core" : 
			
			$Core = new Core((int) $_POST["ID"]);
			$Core->Page; // Wordt geinitialiseerd wanneer deze voor het eerst wordt aangeroepen
			
		break;
		
		
		case "history" : 
			
			History::jumpTo((int) $_POST["offset"]);
			
		break;
		
		
		case "page" : 

			$module = 'SysPage_' . $_POST["page"];		
			if(!class_exists($module)) throw new Exception("Pagina klasse '$module' niet gevonden");
			if(!is_subclass_of($module, "SysPage_Abstract")) throw new Exception("Pagina klasse '$module' is geen subclasse van SysPage_Abstract");
			new $module();
			
		break;
		
		
		case "function" :
			
			$specialFunction = 'Function_' . array_shift(Request::$Path); 
			if(!class_exists($specialFunction)) throw new Exception ("Special Function '$specialFunction' niet gevonden");
			if(!is_subclass_of($specialFunction, "Function_Abstract")) throw new Exception ("Classe '$specialFunction' is geen subclasse van Function_Abstract");			
			new $specialFunction();
			
		break;
		
		
		case "action" :
			/*
			 * action moet eruit, dit moet worden vervangen door serveraction
			 */
			
			$Core = Life::get((int) $_POST["lifeId"]);	
			$action = $_POST["action"];
			
			if(!is_callable(array($Core, $action))) throw new Exception("Actie '$action' op Core met Life ID '$lifeId' bestaat niet");
			if(!$Core->isRequestable($action)) throw new Exception("Actie '$action' op Core met Life ID '$lifeId' mag niet worden aangevraagd");
			
			$Core->$action();
		
		break;		
		
		default :
			throw new Exception("Onjuiste aanvraag naar server. Type aanvraag: '$type'");
	}
}
catch(Access_Exception $E)
{
	Response::error($E);
}	

Response::finish();
