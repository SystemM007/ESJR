<?php

class PhotocollectionPage extends TextPage
{
	protected $historyName = "Fotocollectie Pagina";
	
	private $sectionId;
	
	public function __construct()
	{
		$this->sectionId = Request::$Post["id"];

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
				
		new PhotocollectionsList($this->sectionId);
	}
	
	public static function onSectionCreate($sectionId)
	{
		parent::onSectionCreate($sectionId);
		
		return "PhotocollectionPage";
	}
	
	public static function onSectionDelete($sectionId)
	{
		$n = MySql::select(array(
			"from" => "site_photocollections",
			"where" => "phcolSection = '$sectionId'"
		));
		
		if($n) throw new Exception("De sectie bevat nog fotocollecties!");

		parent::onSectionDelete($sectionId);
	}
}