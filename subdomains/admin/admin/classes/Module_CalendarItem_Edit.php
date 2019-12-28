<?php

class Module_CalendarItem_Edit extends Module_Basic_Edit
{
	protected function makeEditables()
	{	
		$editables = "";
	
		$editables .= $this->coreName("Agendapunt titel");

		$editables .= $this->calendarDate();
		$editables .= $this->calendarText();
		
		if(User::levelAllowed(User::ACCESSLEVEL_HIGHEST)) // sowieso niet te bedoeling!
		{
			//$editables .= $this->coreModule();
			$editables .= $this->coreReadLevel();
			$editables .= $this->coreWriteLevel();
			//$editables .= $this->coreChildCreateLevel();
		}
		
		$editables.= $this->coreEnabled();	
				
		return $editables;
	}
	
	protected function calendarDate()
	{
		$Edit = new Editable_MDate("Datum", array("w_calendar", "ID", $this->getID(), "date"));
		$Edit->checkDateFuture();
		$this->addEditable($Edit);
		return $Edit;
	}
	
	protected function calendarText()
	{
		$Edit = new Editable_Tiny("Tekst", array("w_calendar", "ID", $this->getID(), "text"));
		$this->addEditable($Edit);
		return $Edit;
	}
}