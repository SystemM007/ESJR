<?php

class Module_SettingsContainer_Page extends Module_TwoColumn_Page
{
	protected $textColumnLeft = "Settings in deze website";
	protected $widthColumnLeft = "40%";
	protected $widthColumnRight = "40%";

	protected function buildMessage()
	{
		Response::msg("Hier kunt u de settings van deze website beheren");
	}

	protected function onNoEditables()
	{
		return
		"<h1>Settings beheren</h1>
		<p>Hier vindt u een overzicht van de settings in deze website.</p>";
	}
	
	protected function onNoEdit(){}
	
	protected function onNoChildren(){}
}