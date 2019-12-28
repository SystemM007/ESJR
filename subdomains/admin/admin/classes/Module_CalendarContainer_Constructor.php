<?php

class Module_CalendarContainer_Constructor extends Module_WebPage_Constructor
{	
	protected function getNewCoreName()
	{
		return "Agendapagina";
	}
	
	protected function getNewChildrenAllowed()
	{
		return true;
	}
}