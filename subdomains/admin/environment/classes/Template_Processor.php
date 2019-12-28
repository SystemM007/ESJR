<?php

class Template_Processor
{
	protected $Root; 
	
	protected $fill = array();
	protected $options = array();
	
	public function __construct(Template_Root_Element $Root)
	{
		$this->Root = $Root;
	}
	
	/*
	* Argumenten:
	* 1e is de eerste namespace
	* 2e de tweede namespace
	* (n-2)e is de (n-2)e namespace
	* (n-1)e is de key
	* (ne is de waarde
	*
	* indien twee namespaces gegeven wordt er gevuld in de Root
	*
	*/
	
	public function fill()
	{
		$args = func_get_args();
		$fill = $this->argsToArray($args);
		
		$this->mergeFill($this->fill, $fill);
	}
	
	public function singleFill(array $fills)
	{
		//foreach($fills as $key => $fill) $this->fill($key, $fill);
		$this->fill($fills);
	}
	
	/*
	 * Deze functie kan twee multidementionele arrays non-destructive in elkaar mergen
	 * dus
	 * $array1 (key1 => array(key2 => val1)
	 * $array2 (key1 => array(key3 => val2)
	 * wordt
	 * (key1 => array(key2 => val1, key3 =>val2)
	 */
	
	protected function mergeFill(array &$fillArray, array $fillInsert)
	{
		foreach($fillInsert as $key => $value)
		{			
			if(!isset($fillArray[$key]))
			{
				$fillArray = array_merge($fillArray, array($key =>$value));
			}
			elseif(is_array($fillArray[$key]))
			{
				$this->mergeFill($fillArray[$key], $value);
			}
			else
			{
				throw new Exception("Fill probeert een bestaande waarde over te schrijven");	
			}
		}
	}
	
	/*
	 * Merk op dat fuse nog totaal GEEN waarden overneemt! 
	 * Enkel de TEMPLATE wordt over genomen!
	 */
	public function fuse()
	{
		$args = func_get_args();
		$fuse = $this->argsToArray($args);
		
		$this->Root->fuse($fuse);
	}
	
	/*
	 * Deze functie wordt gebruikt door Template_Location_Element als er gefuseerd wordt.
	 */
	public function getRoot()
	{
		return $this->Root;
	}
	
	/*
	 * Deze functie maakt een nested array van de argumenten
	 * Dit gebeurt alsvolgt
	 * argsToArray("key1", "key2", $fill) wordt
	 * array(key1 => array(key2 => $fill))
	 * 
	 * Let op! Enkel de laatste waarde mag dus geen string zijn
	 */
	
	private function argsToArray($args)
	{
		if(!count($args)) throw new Exception("Er moet minstends één argument gegeven zijn.");
		
		$fill = array_pop($args);
		
		while($key = array_pop($args))
		{
			if(!is_string($key)) throw new Exception("De key is geen string");
			$fill = array($key => $fill);
		}
		
		if(!is_array($fill)) throw new Exception("Wanneer één argument gegeven moet dit een array zijn");
		
		return $fill;
	}
	
	public function __toString()
	{
		try
		{
			return $this->Root->content($this->fill, $this->options);
		}
		catch(Exception $Exception)
		{
			die(nl2br($Exception));
		}
	}
	
	public function tree()
	{
		return $this->Root->tree(); 
	}
}