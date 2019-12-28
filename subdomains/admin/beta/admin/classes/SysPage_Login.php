<?php

class SysPage_Login extends SysPage_Abstract
{
	protected $accessLevel = User::ACCESSLEVEL_ALWAYS_NO_LOGIN;

	public function isRequestable($function)
	{
		return $function == "login" || parent::isRequestable($function);	
	}
	
	protected function construct($errormessage = "")
	{	
		if(User::levelAllowed(User::ACCESSLEVEL_ALWAYS) )
		{	// indien er ingelogd is
			$this->forwardToPage();
			return;
		}
		
		$this->registerLife();
		
		Response::title("Unwritten Admin // Login");
		
		$this->UserName = new Editable_Text("Gebruikersnaam");
		$this->UserName->optionGiveFocus();
		$this->UserPasswd = new Editable_Passwd("Wachtwoord");
		$this->UserPasswd->optionDirectConvert();
		
		Response::template(
			"<h1>Welkom</h1>" .
			"<div style='width:40%; '" .
			$this->UserName .
			$this->UserPasswd .
			"<p><br />" .
			new Fragment_Button_Login($this->getLifeId()) .
			"</p>" .
			"</div>"
		);
		
		Response::onSubmit(new Fragment_JS_Action($this->getLifeId(), "login", array(), array("loadEditables" => true)));
		
		if(!$errorMessage) $errorMessage = Request::$Post["errorMessage"];
		
		if($errorMessage)
		{
			Response::tip("<h2>Login fout opgetreden</h2><strong>Terwijl u aan het werken was trad er een login fout op. Mogelijk heeft u in een ander venster op 'afmelden' geklikt. <br />Bericht: </strong>" . $errorMessage);
		}
	}
	
	public function login()
	{
		// naam
		try
		{
			$username = $this->UserName->getInput();
		}
		catch(Editable_Exception $e)
		{
			$error = $e->getMessage();	
		}
		if(!$error && !isset($username))
		{
			$error = "Vul een gebruikersnaam in";
		}
		if($error)
		{
			Reponse::msg("Fout bij inloggen");
			Response::tip("<h2>Gebruikersnaam fout</h2><p>$error</p>");
			$this->UserName->feedbackError();
		}
		
		// wachtwoord
		try
		{
			$passwordh = $this->UserPasswd->getInput();
		}
		catch(Editable_Exception $e)
		{
			$error = $e->getMessage();	
		}
		if(!$error && !isset($passwordh))
		{
			$error = "Vul een wachtwoord in";
		}
		if($error)
		{
			Reponse::msg("Fout bij inloggen");
			Response::tip("<h2>Wachtwoord fout</h2><p>$error</p>");
			$this->UserPasswd->feedbackError();
		}
		
		// controle
		$userdata = MySql::selectRow(array(
			"select" => array("userName", "password", "userLevel"),
			"from" => "u_adminusers",
			"where" => "`userName` = '$username'"
		));
	
		if(!$userdata || $userdata["password"] != md5($passwordh))
		{
			Response::tip("<h2>Wachtwoord fout</h2>Wachtwoord voor '$username' is incorrect.<br />" );
			$this->UserPasswd->feedbackError();
			$this->UserName->feedbackError();
			return;
		}

		
		User::writeCookie($userdata["userName"], $passwordh);
		
		$this->UserName->feedbackSaved();
		$this->UserPasswd->feedbackSaved();
		Response::evalJS( new Fragment_JS_Core(Settings::get("rootCoreID") ) );
		Response::msg("Login Correct");
		
		
		try
		{
			if(time() - Settings::get("last_backup") > 86400)
			{
				new DatabaseBackup("Backup bij inloggen admin.\nGebruiker: " . User::getName());
			}
			FB::info("Er is een nieuwe backup gemaakt van de database");
		}
		catch(Exception $E)
		{
			// deze backup wordt "stil" gemaakt.
			try
			{
				mail("leon@jongdesigns.nl", "Fout bij het maken van backup op " . Uri::abs_host, $E->getMessage(), "From: backup@jongdesigns.nl");
			}
			catch(Exception $E){}
		}
	}

	private function forwardToPage()
	{
		// merk op dat deze pagina geen geschiedenis maakt!!
		if(!History::jumpToLatestGlobal())
		{
			$Core = new Core((int) Settings::get("rootCoreID"));
			$Core->Page;
		}
	}
}