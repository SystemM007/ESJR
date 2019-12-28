<?php

class Module_UserContainer_Page extends Module_TwoColumn_Page
{
	protected $textColumnRight = "Gebruikers in deze admin";
	protected $widthColumnLeft = "40%";
	protected $widthColumnRight = "60%";
	
	protected function buildMessage()
	{
		Response::msg("Hier kunt u de gebruikers van de admin beheren");
	}

	protected function onNoChildren(){}

	protected function onNoEdit()
	{
		return
		"<h1>Gebruikers beheren</h1>
		<p>Hier vindt u een overzicht van de gebruikers van de admin.</p>
		<p>Gebruikers zijn er in verschillende typen.
		Sommigen mogen meer dan anderen. Hierdoor komt het dat u niet alle gebruikers kunt verwijderen of bewerken.</p>
		<h3>Wachtwoorden</h3>
		<p>Van alle gebruikers waar u rechten over heeft kunt u het wachtwoord wijzigen. Ook dat van uzelf dus!
		Klik op uw eigen naam om het wachtwoord wijzigen.</p>
		<p><strong>Merk op:</strong> uw wachtwoord is <em>nooit</em> zichtbaar
		voor andere gebruikers (dus ook niet als ze het kunnen wijzigen).</p>";
	}
}