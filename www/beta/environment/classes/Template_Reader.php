<?php

class Template_Reader extends Template_Abstract_Reader
{
	public function __construct($file)
	{
		parent::__construct(array(), $this->getFileContent($file), $file);
	}
	
	public function makeElement()
	{
		throw new Exception("Kan makeElement niet aanroepen op Template_Reader! Om de Root te krijgen: gebruik getRoot");
	}
	
	protected function addTag($tagName, array $attributes, $innerContent)
	{
		if(count($this->elements)) throw new Template_Exception("Kan niet meer dan 1 tag toevoegen op Root niveau", $this);
		if($tagName != "template") throw new Template_Exception("Kan geen '$tagName' aanmaken op Root niveau", $this);
	
		$Reader = new Template_Root_Reader($attributes, $innerContent, $this->getFile(), $this->getReadLine());	
		
		$this->addElement($Reader->makeElement());
	}
	
	protected function addString($string)
	{
		// hier zou je errors kunnen spugen...
	}
	
	protected function getFileContent($file)
	{
		$content = file_get_contents($file);

		$constants = Uri::$constants;
		
		$search = array_keys($constants);
		$replace = array_values($constants);
		
		foreach($search as $key => $val) $search[$key] = "%$val%";
		
		$content = str_replace($search, $replace, $content);
		
		return $content;
	}
	
	public function getRoot()
	{
		return $this->elements[0];
	}
	
	public function getProcessor()
	{
		return new Template_Processor($this->getRoot());
	}
}