<?php
class Section_Robots extends Section_Abstract
{
	public function __construct($ID)
	{
		header("Content-Type: text/plain; charset=utf-8");
		
		if(preg_match("/^www\.beta/", $_SERVER["HTTP_HOST"]))
		{
			$this->disallow();
		}
		else
		{
			$this->noRobots();
		}
	}
	
	protected function disallow()
	{
		echo new Template("robotsDisallow");
	}
	
	protected function noRobots()
	{
		Request::give404("Robots niet actief op 'final' website.");
	}
	
	
}
