<?php

class WsSection extends TextPage
{
	
	protected $historyName = "Webshop Afdeling";

	
	public static function onSectionCreate($sectionId)
	{
		parent::onSectionCreate($sectionId);
		
		return "WebshopHome";
	}
}