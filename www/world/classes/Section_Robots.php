<?php
class Section_Robots extends Section_Abstract
{
	public function __construct($ID)
	{
		header("Content-Type: text/plain; charset=utf-8");
		
		if(preg_match("/^www\.beta/", $_SERVER["HTTP_HOST"]))
		{
			$this->betaRobots();
		}
		else
		{
			$this->finalRobots();
		}
	}
	
	protected function betaRobots()
	{
		echo new Template("robotsDisallow");
	}
	
	protected function finalRobots()
	{
		Request::give404("Robots niet actief op 'final' website.");
	}
	
	
}
