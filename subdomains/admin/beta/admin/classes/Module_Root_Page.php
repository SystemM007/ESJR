<?php

class Module_Root_Page extends Module_TwoColumn_Page
{
	protected $textColumnRight = "Beheer: ";
	
	protected $BackupTime;
	
	protected function makeStaticLayout()
	{
		parent::makeStaticLayout();
		
		Response::msg("Welkom " . User::getName());
	}
	
	protected function makeTemplate(array $fill = array())
	{
		$fill = array_merge(array("widthLeft" => "40%", "widthRight" => "60%"), $fill);
		parent::makeTemplate($fill);
	}
	
	protected function onNoChildren()
	{
		return "DOOO";
	}

	protected function onNoEdit()
	{
		$this->BackupTime = new Location("", true);
		$this->updateBackupTime();
		
		return "
			<h1>Welkom in de Jong Designs Admin</h1>
			<p>In deze admin kunt u de inhoud uw website beheren.<br/>
			Ook kunt u de gebruikers van de admin beheren en afbeeldingen inladen. Klik hiervoor op respectievelijk Gebruikers en Afbeeldingen</p>
			<p>Mocht u vragen hebben, neemt u dan gerust contact op met Leon. Rechts bovenaan de pagina vindt u een help-knop.</p>
			
			<h2>Backup</h2>
			<p>De laatste keer dat er een backup van de gegevens op de website is gemaakt was: </p>
			<p><strong>" . $this->BackupTime . "</strong></p>
			<p><a href='Javascript:void(0);' onclick='" . new Fragment_JS_Action($this->getLifeId(), "makeBackup") . "'>Maak nu een backup</a></p>
			";
	}
	
	public function getButtons()
	{
		return (string) new Fragment_Button_Action("Maak backup", $this->getLifeId(), "makeBackup");
	}
	
	public function isRequestable($function)
	{
		return $function == "makeBackup" || parent::isRequestable($function);
	}
	
	public function makeBackup()
	{
		try
		{
			new DatabaseBackup("Backup vanaf rootpagina admin.\nGebruiker: " . User::getName());
		}
		catch(Exception $E)
		{
			Response::msg("NIET gelukt: {$E->getMessage()}");
			return;
		}
		
		Response::msg("Backup succesvol gemaakt.");
		if($this->BackupTime) $this->updateBackupTime();
	}
	
	private function updateBackupTime()
	{
		try
		{
			$this->BackupTime->update(date("d-m-Y H:i:s", Settings::get("last_backup")));
		}
		catch(Exception $E)
		{
			$this->BackupTime->update("Maak instelling last_backup aan!");
		}
	}

}