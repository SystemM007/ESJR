<?php
class Ofc_Object
{
	protected $data = array();
	
	final public function getData()
	{
		return $data;
	}
	
	public function __construct($data = NULL)
	{
		if(isset($data)) $this->addDataFields($data);
	}
	
	final protected function addDataFields(array $data)
	{
		foreach(array_keys($data) as $key) if(isset($this->data[$key])) throw new Exception("Key '$key' is already defined");
		$this->data = array_merge($this->data, $data);
	}
}
?>