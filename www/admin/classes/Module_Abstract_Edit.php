<?php

abstract class Module_Abstract_Edit extends Module_Core
{	
	private $editables = array();
	protected $firstEdit;
	
	final public function __construct(Core $Core)
	{
		parent::__construct($Core);
		$this->Core->checkWriteAccess("Openen Edit Module");
		$this->registerLife();
		Response::onSubmit(new Fragment_JS_Action($this->getLifeId(), "save", array(), array("loadEditables" => true)));
	}
	
	final public function __wakeUp()
	{
		$this->Core->checkWriteAccess("Ontwaken Edit Module");
	}
		
	final protected function addEditable(Editable_Abstract $editable)
	{
		$this->editables[] = $editable;
	}
	
	// ------ Requestable -----
	
	public function isRequestable($function)
	{
		return in_array($function, array("save"));
	}
	
	final public function save()
	{
		$errorTips = "";
		$fieldsSaved = false;
		
		foreach($this->editables as $Editable)
		{
			try
			{
				$saved = $Editable->save();
			}
			catch(Editable_Exception $e)
			{
				$errorTips .= "<h3>" . $Editable->getName() . "</h3><p>" . $e->getMessage() . "</p>";
				continue;
			}
			
			$fieldsSaved = $fieldsSaved || $saved;
		}
		
		if($errorTips)
		{
			Response::msg("Er waren fouten in de invoer.");
			Response::tip("<h2>Fouten in de invoer: </h2>$errorTips");
		}
			
		if( $fieldsSaved )
		{
			Response::msg("Er is succesvol opgeslagen");
		}
		elseif(!$errorTips)
		{
			Response::msg("Er zijn geen aanpassingen gedaan");
			Response::tip("<h2>Aanpassingen doen</h2><p>Voordat u kunt opslaan moet u een van de lichtblauw omlijnde velden van de pagina bewerken. Klik op het veld om een bewerking aan te brengen</p>");
		}
	}
}