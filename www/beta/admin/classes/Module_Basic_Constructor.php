<?php

abstract class Module_Basic_Constructor extends Module_Abstract_Constructor
{
	// still abstract
	// protected function getNewCoreName(){}

	protected function beforeDelete()
	{
	}
	
	protected function onDelete()
	{
	}

	protected function beforeCreate()
	{
	}
	
	protected function onCreate()
	{
	}
	
	protected function getNewReadLevel()
	{
		return NULL;
	}
	
	protected function getNewWriteLevel()
	{
		return NULL;
	}
	
	protected function getNewChildrenAllowed()
	{
		return true;
	}
	
	protected function getNewChildCreateLevel()
	{
		return NULL;
	}
	
	protected function getNewEnabled()
	{
		return true;
	}
}