<?php

class CalendarPage extends TextPage
{
	
	protected $historyName = "Kalender Pagina";
	
	public function __construct()
	{
		parent::__construct();
		
		$this->create();
	}
	
	private function create()
	{
		Response::template(
			"<h2>Nieuw item invoegen</h2>" .
			new Div("newLink") .
			new Div("list") .
			new Div("fullLink") 
		);
		
		new CalendarItemList();
	}
	
	public static function onSectionCreate($sectionId)
	{
		parent::onSectionCreate($sectionId);
		
		return "CalendarPage";
	}
	
	public static function onSectionDelete($sectionId)
	{
		parent::onSectionDelete($sectionId);
		
		// ik verwijder NIET alle calendar items!
	}
}