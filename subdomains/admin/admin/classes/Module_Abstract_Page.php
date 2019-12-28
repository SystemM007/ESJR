<?php

abstract class Module_Abstract_Page extends Module_Core
{
	protected $historyName = "Terug"; // geef hier de naam die op een knop van de volgende pagina, naar deze pagina kan staan!
	protected $historyKey;
	
	public function isRequestable($function)
	{
		return false;
	}
	
	final public function __construct(Core  $Core)
	{
		parent::__construct($Core);
		
		$this->Core->checkReadAccess("Openen van Page '".$this->Core->ID ."' met module '". $this->Core->module ."'");
	
		$this->getPost();
		$this->getData();
	
		$this->life();	
		$this->makeHistoryBar();
		
		$this->buildPage();
	}
	
	final public function __wakeUp()
	{
		$this->Core->checkReadAccess("Opwekken van Page");
	}
	
	protected function getPost()
	{
	}

	protected function getData()
	{
	}
	
	final private function life()
	{
		Life::killAll();
		$this->registerLife();
			
		if(isset($this->historyKey)) throw new Exception("initPage voor de tweede maal aangeroepen");
		$this->historyKey = History::make($this->historyName, $this);
	}
	
	abstract protected function buildPage();
	
	final protected function makeHistoryBar()
	{
		$bar = array();
		$depth = count(Session::$instance["history"]);
		
		for($offset = count(Session::$instance["history"]) -1 ; $offset >= 0; $offset--)
		{
			$bar[] = new Fragment_Tag_HistoryJumpLink($offset);
		}
		
		Response::pagenumbers(implode(" &gt;&gt; ", $bar));
	}
	
	// util
	protected function setHistoryName($name)
	{
		$this->historyName = $name;
		if(isset($this->historyKey))
		{
			History::changeName($this->historyKey, $name);
			$this->makeHistoryBar();
		}
	}
	
	
	// ook public maken?
	final protected function historyChange($history)
	{
		if(!isset($this->historyKey)) throw new Exception("historyChange kan niet worden aangeroepen voordat history gemaakt is");
		History::change($this->historyKey, $history);
	}
}