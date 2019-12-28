<?php

class Template_Root_Element extends Template_Collector_Element
{	
	protected $version;
	
	public function __construct(array $elements, $version = NULL)
	{
		$this->version = $version;
		parent::__construct($elements);
	}
	
	public function getVersion()
	{
		return $this->version;
	}

	public function tree($depth = 0)
	{
		$return = $this->objectAsTree($depth) . "[v$this->version]";
		$return .= $this->elementsAsTree($depth);
		return $return;
	}
}