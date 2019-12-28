<?php
class Section_Crossdomain extends Section_Abstract
{
	public function __construct($ID)
	{
		header("Content-Type: text/xml; charset=utf-8");
		echo new Template("crossdomain");
	}
}