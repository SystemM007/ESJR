<?php

class Fragment_ErrorPage extends Fragment_Abstract
{
	private $Template;
	
	public function __construct($erno, $msg)
	{
		switch($erno)
		{
			case 404 : $template = "error404";
			break;
			case 403 : 
			default  : $template =  "error403";
		}
		
		$this->Template = new Template($template, array("msg" => $msg));
	}
	
	public function create()
	{
		return (string) $this->Template;
	}
	
}