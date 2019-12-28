<?php

class Section_ImagePage extends Section_WebPage
{
	public function __construct($ID)
	{
		parent::__construct($ID);
		
		$this->Template->fill("left", new Fragment_PageImages($this));
	}
}