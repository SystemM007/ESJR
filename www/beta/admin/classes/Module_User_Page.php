<?php

class Module_User_Page extends Module_TwoColumn_Page
{
	protected $textColumnLeft = "Deze gebruiker";


	protected function onNoChildren(){}

	protected function onNoEdit()
	{
		return
		"<h1>Niet bewerken</h1>
		<p>U kunt de instellingen voor deze gebruiker niet bewerken.<br/>
		U kunt alleen de instellingen van gebruikers bewerken die evenveel of minder rechten hebben dan uzelf.</p>";
	}
}