<?php

class Module_WebPage_Page extends Module_TwoColumn_Page
{

	protected function onNoEdit()
	{
		return
			"<h2>Onderliggende afdelingen van " . $this->Core->name . "</h2>";
	}
	
	protected function onNoChildren()
	{
	}
	
	/*
	
	// Leeeeuuukk! De pagina titel in de titelbalk van de admin... 
	
	protected function getData()
	{
		$ID = $this->Core->ID;
		
		$this->sectionTitle = MySql::selectValue(array(
			"select" => "title",
			"from" => "u_webpage",
			"where" => "`ID` = '$ID'"
		));
	}
	
	public function makeChangeableLayout()
	{
		parent::makeChangeableLayout();
		Response::title("$this->sectionTitle");
	}
	*/
}