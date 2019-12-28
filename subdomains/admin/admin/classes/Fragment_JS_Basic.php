<?php

class Fragment_JS_Basic extends Fragment_Abstract {

	private $confirm = "";
	private $funct = "";
	private $args = array();
	private $noEncodeFields = array();

	public function __construct()
	{
		$args = func_get_args();
		$funct = array_shift($args);;
		$this->funct = $funct ? $funct : $this->funct;
		$this->args = $args ? $args : $this->args;
	
	}
	
	public function setFunction($function)
	{
		$this->funct = $function;
	}
	
	public function setArgs()
	{
		foreach(func_get_args() as $arg) $this->addArg($arg);
	}
	
	public function addArg($arg, $escape = true)
	{
		if($escape) $arg = json_encode($arg);
		$this->args[] = $arg;
	}
	
	public function setConfirm($confirm)
	{
		$this->confirm = (string) addcslashes($confirm, '"');
	}
	
	public function create()
	{
		$comm = $this->funct . "(" . implode(", ", $this->args) . "); ";
		
		if($this->confirm){
			$comm = "if(confirm(\"$this->confirm\")){ $comm }";
		}
		
		return $comm;
	}

	
}