<?php

class Module_ImagesContainer_Page extends Module_TwoColumn_Page
{
	public function makeChangeableLayout()
	{
		parent::makeChangeAbleLayout();
	}
	
	protected function onNoChildren(){}
	
	protected function onNoEdit()
	{
		return
		"<h1>Afbeeldingen voor op de website</h1>
		<p>Hier kunt u afbeeldingen inladen voor op de website.</p>
		<h3>Type</h3>
		<p>Afbeeldingen <em>moeten</em> JPG afbeeldingen zijn</p>
		<h3>Meerdere afbeeldingen</h3>
		<p>Tip! U kunt meerdere afbeeldingen selecteren in het afbeeldingen dialoog. <br />
		Ook is het mogelijk opnieuw op de knop te klikken als er nog een afbeelding bezig is met uploaden. 
		Deze wordt dan in de rij gezet, en zal gaan uploaden na de afbeelding die bezig is.</p>";
	}
}