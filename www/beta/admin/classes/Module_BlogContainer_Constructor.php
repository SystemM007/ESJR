<?php

class Module_BlogContainer_Constructor extends Module_WebPage_Constructor
{	
	protected function getNewCoreName()
	{
		return "Columns";
	}
	
	protected function getNewChildrenAllowed()
	{
		return true;
	}
}