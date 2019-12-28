<?php

abstract class Module_Basic_Children extends Module_Abstract_Children implements Module_Basic_Children_Interface
{
	private $LocationTable;
	private $LocationCreateInput;
	protected $displayCoreName = true;
	protected $displayCoreEnabled = false;
	protected $sortable = false;
	
	/*
	* wordt aangeroepen vanuit TwoColumn_Page
	* retourneert bij genoeg rechten om children te maken de output van makeCreateInput
	* @return string HTML
	*/
	final public function getCreateInput()
	{
		if(isset($this->LocationCreateInput))
		{
			throw new Exception("getCreateInput voor de tweede maal aangeroepen.");
		}
		
		if($this->Core->childCreateAccess())
		{
			return (string) $this->LocationCreateInput = new Location($this->makeCreateInput());
		}
	}
	
	/*
	 * wordt aangeroepen vanuit TwoColumn_Page
	* de implementatie van deze functie dient een stuk HTML te retourneren
	* waarmee de gebruiker children kan aanmaken
	* @return string HTML
	*/
	abstract protected function makeCreateInput();

	/*
	* Deze functie kan worden aangeroepen vanuit TwoColumn_Page
	* maar dit kan slechts ��n keer gebeuren!
	* daarna is er in dit object de eigenschap LocationTable beschikbaar waarin de tabel staat
	* en is er de methode refresh
	* @return string HTML
	*/
	final public function getTable()
	{
		if(isset($this->LocationTable))
		{
			throw new Exception("getTable voor de tweede maal aangeroepen.");
		}
		
		$this->adminListId = "adminList_" . uniqid();
	
		return $this->LocationTable = new Location($this->makeTable());
	}
	
	/*
	* Kan html retourneren die in de knoppen balk wordt gezet
	* @return string HTML
	*/
	public function getButtons()
	{
		$buttons = "";
		
		if(User::levelAllowed(User::ACCESSLEVEL_HIGHEST))
		{
			$buttons .= new Fragment_Button_Action("Verwijder alle onderliggende afdelingen", $this->getLifeId(), "deleteAllChildren", array(), array(), "Weet u HEEL, HEEL zeker dat u alle onderliggende afdelingen verwijderen wilt?!");
		}
		
		return $buttons;
	}
	
	
	public function refresh()
	{
		$this->refreshTable();	
		
	}
	
	/*
	* ververst de tabel
	* @return void
	*/
	protected function refreshTable()
	{
		if(!isset($this->LocationTable))
		{
			throw new Exception("refreshTable aangeroepen voordat de tabel is gemaakt.");
		}
		
		$this->LocationTable->update($this->makeTable());
	}
	
	/*
	* ververst de create input
	* WORDT STANDAARD NIET AANGEROEPEN!
	* @return void
	*/
	protected function refreshCreateInput()
	{
		if(!isset($this->LocationCreateInput))
		{
			throw new Exception("refreshCreateInput aangeroepen voordat de tabel is gemaakt.");
		}
		
		$this->LocationCreateInput->update($this->makeCreateInput());
	}
	
	/*
	* Genereert de HTML voor de lijst
	* @return string HTML
	*/
	final private function makeTable()
	{
		$sortable = $this->Core->childCreateAccess() && $this->sortable;
		
		Response::childList($this->adminListId, array(
			"sortable" => $sortable, 
			"lifeId" => $this->getLifeId(),
			"action" => "order",
		));
		
		$data = $this->selectData();
		$data = $this->processData($data);
		
		$deleteRows = $this->getDeleteRows($data);
		$clickCoreRows = $this->getClickCoreRows($data);
		$data->unsetColumns(array("constructLevel", "writeLevel"));
		
		$coresEnabledField = $this->displayCoreEnabled ? "coreEnabled" : false;
		
		$List = new Fragment_AdminList($data, "ID", $coresEnabledField, "adminList", $this->headers());
			
		$List->addDeleteField($this->Core, "deze afdeling", $deleteRows);
		$List->onClickCore(array(), array(), NULL, NULL, $clickCoreRows);
		$List->sortable($this->adminListId, $sortable);
		

		return (string) $List;
	}
	
	/*
	* @return Dataset met de informatie voor de lijst
	*/
	final private function selectData()
	{
		// de functie listExtention is depreciated
		// deze onderstaande regel mag weg wanneer deze niet meer gebruikt wordt.
		// de argumenten in de functies listSelect, listJoin en listWhere moeten dan weg worden gelaten
		list($select, $join, $where) = $this->listExtention();
		
		$select = $this->listSelect($select);
		$select = array_merge($select, array("u_cores.ID", "u_cores.writeLevel", "u_modules.constructLevel"));
		if($this->displayCoreName) $select[] = "u_cores.name as coreName";
		if($this->displayCoreEnabled) $select[] = "u_cores.enabled as coreEnabled";
		
		$from = "u_cores";
		
		$join = $this->listJoin($join);
		$join[] = array("table" => "u_modules", "on"=>"u_cores.module = u_modules.module") ;
		
		$where = $this->listWhere($where);
		$where = "( u_cores.readLevel >= '". User::getLevel() . "' OR u_cores.readLevel < '0') " .
				"AND u_cores.parent='". $this->Core->ID ."' " . 
				$where;
		
		$order  = $this->listOrder();
		
		return MySql::select(compact("select", "from", "join", "where", "order"));
	}
	
	/*
	* DEPRECIATED
	* Vervang deze functie door de drie onderstaande equivalenten
	*/
	protected function listExtention(array $select = array(), array $join = array(), $where = "")
	{
		return array($select, $join, $where);
	}
	
	/*
	* @return array van database velden die moeten worden geselecteerd naast de standaard velden
	*/
	protected function listSelect(array $select = array())
	{
		return $select;
	}
	
	/*
	* @return array met daarin join commando's  welke moeten gebruikt n��st de standaard join die gedaan wordt
	*/
	protected function listJoin(array $join = array())
	{
		return $join;
	}
	
	/*
	* biedt mogelijkheid voorwaarden aan te passen
	* @return string met voorwaarden die moet worden gebruikt naast de standaard voorwaarden
	*/
	protected function listWhere($where = "")
	{
		return $where;
	}
	
	/*
	* biedt mogelijkheid sortering aan te passen
	* @return string met de sortering die moet worden toegepast
	*/
	protected function listOrder($order = "")
	{
		return $order ? $order : "u_cores.order, u_cores.name";
	}
	
	/*
	* biedt mogelijkheid de rijen in te stellen van cores die mogen worden verwijderd
	* @return array met rowId's van alle rijen waar een delete knop moet worden ingevoegd
	*/
	protected function getDeleteRows($data)
	{
		// condities leeg maken en lege row set aanmaken
		$rows = array();
		
		// als de gebruiker geen child create toegang heeft
		// dan sowieso geen rechten op verwijderen
		if(User::levelAllowed($this->Core->childCreateLevel) ) 
		{
			// anders voor iedere rij kijken of de gebruiker
			// - constructie rechten heeft voor die Module
			// - schrijfrechten heeft voor die core
			foreach($data as $rowId => $row)
			{
				extract($row);
				if(User::levelAllowed($constructLevel) && User::levelAllowed($writeLevel))
				{
					$rows[] = $rowId;
				}
			}
		}
		
		return $rows; 
	}
	
	/*
	* biedt mogelijkheid de klikbare rijen in te stellen
	* @return array met rowId's van alle rijen waar na klikken een core moet worden geopend
	* @param Dataset $data: verwerkte set met data met daarin nog twee kolomen met rechten
	*/
	protected function getClickCoreRows($data)
	{
		return $data->getRowHeaders();
	}
	
	/*
	* biedt mogelijkheid andere headers toe te voegen
	* @return array met daarin key => value van databaseVeld => naam van de kolom
	* @param Dataset $data: verwerkte set met data met daarin nog twee kolomen met rechten
	*/
	protected function headers(array $headers = array())
	{
		if($this->displayCoreName)
		{
			$headers = array_merge( array("coreName" => "Naam"), $headers) ;
		}
		
		return $headers;
	}
	
	/*
	* biedt mogelijkheden de dataset aan te passen voordat deze in de lijst komt
	* @return Dataset $data bewerkte dataset
	* @param Dataset $data oorspronkelijke dataset
	*/
	protected function processData(Dataset $data)
	{
		return $data; 
	}
	
	public function isRequestable($function)
	{
		return($function == "order" || parent::isRequestable($function));
	}
	
	public function order()
	{
		if(!($this->Core->childCreateAccess()  && $this->sortable)) throw new Exception("Order aangeroepen op niet sorteerbare childlist");

		$childListOrder = Request::$Post["childListOrder"];
		$childListOrder = json_decode($childListOrder, true);
		
		foreach($childListOrder as $position => $ID)
		{
			MySql::update(array(
				"table" => "u_cores",
				"values" => array(
					"order" => $position
				),
				"where" => "ID = '$ID'"
			));
		}
	}
}