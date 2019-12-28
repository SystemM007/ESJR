<?php

class Module_Setting_Page extends Module_TwoColumn_Page
{
	protected $textColumnLeft = "Deze gebruiker";
	
	protected function onNoEdit()
	{
		return
		"<h1>Niet bewerken</h1>
		<p>U kunt de instellingen voor deze setting niet bewerken.</p>";
	}
		
	protected function onNoChildren(){}

}