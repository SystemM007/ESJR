<?php

class Module_ModuleContainer_Page extends Module_TwoColumn_Page
{
	protected function onNoEditables()
	{
		return
		"<h1>Modules beheren</h1>
		<p>Hier vindt u een overzicht van de modules van de admin.</p>";
	}
	
	protected function onNoEdit(){}
	
	protected function onNoChildren(){}
}