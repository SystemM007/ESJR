<?php

abstract class Template_Abstract_Element
{
	protected $Container;
	
	abstract public function __construct();
	
	abstract public function content(array $fill, array $options);
	
	abstract public function fuse(array $fuse);
	
	public function tree($depth = 0)
	{
		return $this->objectAsTree($depth);
	}
	
	final protected function objectAsTree($depth)
	{
		return "\n" . str_repeat("\t", $depth) . get_class($this);
	}
}