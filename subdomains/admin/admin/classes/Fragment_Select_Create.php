<?php

class Fragment_Select_Create extends Fragment_Select_ServerAction
{
	public function __construct(Matrix $selectorData, $moduleField, $nameField, Core $Core, array $options = array(), $command = "Maak een keuze: ", $confirm = false)
	{
		/*
		 * Voor iedere module een actie aanmaken, en het actie-id opslaan op de plek van de modulenaam
		 */
		foreach($selectorData as $rowId => $row)
		{
			$module = $selectorData->getValue($rowId, $moduleField);
			$ServerAction = new ServerAction(array($Core->Children, "create"), $module);
			$selectorData->update((string)$ServerAction, $moduleField, $rowId);
		}
			
		parent::__construct($selectorData, $moduleField, $nameField, $options, $command, $confirm);
	}
	
	
}