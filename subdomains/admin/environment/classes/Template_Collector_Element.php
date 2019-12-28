<?php

abstract class Template_Collector_Element extends Template_Abstract_Element
{	
	protected $elements;
	
	public function __construct(array $elements = array())
	{
		$this->elements = $elements;
	}
	
	public function content(array $fill, array $options)
	{
		return $this->elementsToContent($fill, $options);
	}
	
	final protected function elementsToContent(array $fill, array $options)
	{
		$content = "";
		foreach($this->elements as $element)
		{
			if(is_string($element)) $content .= $element;
			else $content .= $element->content($fill, $options);
		}
		return $content;	
	}
	
	public function fuse(array $fuse)
	{
		foreach($this->elements as $element)
		{
			if(!is_string($element)) $element->fuse($fuse);
		}
	}
	
	public function tree($depth = 0)
	{
		$return = $this->objectAsTree($depth);
		$return .= $this->elementsAsTree($depth);
		return $return;
	}
	
	final protected function elementsAsTree($depth)
	{
		foreach($this->elements as $element)
		{
			if(is_string($element)) $return .= "\n" . str_repeat("\t", $depth + 1) . "string";
			if(is_object($element)) $return .= $element->tree($depth + 1);
		}
		
		return $return;
	}
}