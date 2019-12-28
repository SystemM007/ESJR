<?php

class PortfolioPage extends TextPage
{

	
	public function __construct()
	{
		parent::__construct();
		
		Response::template(
			"<h2>Portfolio items</h2>" .
			"<h3>Toevoegen</h3>" .
			new Div("upload") .
			new Div("list") .
			new Div("fullLink")
		);
		
		new PortfolioItemList();
	}
	
	public static function onSectionCreate($sectionId)
	{
		parent::onSectionCreate($sectionId);
		
		return "PortfolioPage";
	}
	
	public static function onSectionDelete($sectionId)
	{
		parent::onSectionDelete($sectionId);

	}
}