<?php
/*
 * Deze klasse is geklust om zo zelf een nieuwe 
 * klasse te kunnen schrijven, niet gebaseerd op een Basic klasse,
 * maar wel met de Basic interface.
 * 
 * handig als de children bijvoorbeeld niet in een tabel moeten
 */
interface Module_Basic_Edit_Interface
{
	public function getButtons();
	
	public function getEditables();
}
?>