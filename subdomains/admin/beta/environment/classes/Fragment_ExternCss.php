<?php

class Fragment_ExternScript extends Fragment_Abstract
{
	protected $path;
	
	public function __construct($path)
	{
		$this->path = (string) $path;	
	}
	
	public function create()
	{
		return '<script type="text/javascript" src="' . $this->path . '"></script>';
	}
}