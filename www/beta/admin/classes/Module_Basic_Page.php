<?php

abstract class Module_Basic_Page extends Module_Abstract_Page
{
	// deze andere naam geven?
	final protected function buildPage()
	{
		if($this->Core->writeAccess() && ! $this->Core->Edit instanceof Module_Basic_Edit_Interface)
		{
			throw new Exception(get_class($this->Core->Edit) . " is geen implementatie van Module_Basic_Edit_Interface");
		}
			
		if($this->Core->childrenAllowed && ! $this->Core->Children instanceof Module_Basic_Children_Interface)
		{
			throw new Exception(get_class($this->Core->Children) . " is geen implementatie van Module_Basic_Children_Interface");		
		}
		
		// titel en naam
		$this->buildName();
		
		// knoppen
		$this->buildButtons();
		
		// template
		$this->buildTemplate();
		
		$this->buildMessage();
		
		if(User::levelAllowed(User::USERLEVEL_HIGHEST)) Response::msg("ID: " . $this->Core->ID);
	}
	
	protected function buildButtons()
	{
		Response::buttons(
			new Fragment_Button_HistoryJump()
		);
		
		Response::buttons($this->getButtons());
		
		if($this->Core->writeAccess())
		{
			Response::buttons($this->Core->Edit->getButtons() );
		}
		
		if($this->Core->childrenAllowed)
		{
			Response::buttons($this->Core->Children->getButtons() );
		}
	}
	
	protected function getButtons(){}
	
	public function buildName()
	{
		// buiten haakjes gewerkt om zo
		// te kunnen worden gerefreshed of overschreven waar nodig
		Response::title($this->Core->name);
		$this->setHistoryName($this->Core->name);
	}
	
	abstract protected function buildTemplate();
	
	final protected function getEdit()
	{	
		if($this->Core->writeAccess())
		{
			return (string) $this->makeEdit();
		}
		else
		{
			return (string) $this->onNoEdit();
		}
	}
	
	abstract protected function makeEdit();
	abstract protected function onNoEdit();
	
	final protected function getChildren()
	{
		if($this->Core->childrenAllowed)
		{
			return (string) $this->makeChildren();
		}
		else
		{
			return (string) $this->onNoChildren();
		}
	}
	
	abstract protected function makeChildren();
	abstract protected function onNoChildren();
	
	protected function buildMessage()
	{
	}
}