<?php

class Template_Namespace_Element extends Template_Collector_Element
{	
	protected $key;
	
	public function __construct(array $elements, $key)
	{
		parent::__construct($elements);
		$this->key = $key;
	}
	
	public function content(array $fill, array $options)
	{
		if($fill[$this->key])
		{
			if(!is_array($fill[$this->key])) throw new Exception("De fill namespace '$this->key' is geen array.");
			return $this->elementsToContent($fill[$this->key], $options);
		}
		else
		{
			return "";
		}
	}
	
	public function fuse(array $fuse)
	{
		if(isset($fuse[$this->key]))
		{
			if(!is_array($fuse[$this->key])) throw new Exception("Kan niet fuseren op namespace '$this->key', fuse is geen array");
			parent::fuse($fuse[$this->key]);
		}
	}
	
	public function tree($depth = 0)
	{
		$return = $this->objectAsTree($depth) . "[" .$this->key . "]";
		$return .= $this->elementsAsTree($depth);
		return $return;
	}
}