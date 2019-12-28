<?php

class Editable_AccessSelect extends Editable_Select
{
	protected function onConstruct()
	{
		$userLevel = User::getLevel();
		if($userLevel >= 0)
		{
			$this->optionTable("u_adminAccesslevels", "accessLevel", "name", "u_adminAccesslevels.accessLevel >= '". User::getLevel() ."'");
		}
		else
		{
			throw new Exception("AccessSelects mogen niet worden geinstantieert wanneer de gebruiker niet is ingelogd");
		}
	}
}