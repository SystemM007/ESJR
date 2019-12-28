<?php

class Module_WorkgroupContainer_Constructor extends Module_WebPage_Constructor
{	
	protected function getNewCoreName()
	{
		return "Werkgroepen";
	}
	
	protected function getNewChildrenAllowed()
	{
		return true;
	}
}