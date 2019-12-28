<?php

class CalendarItemList extends ListModule
{
	private $showFullList = false;
	
	private $locationList;
	private $locationNewLink;
	private $locationFullLink;
	

	public function __construct($locationList = "list", $locationNewLink = "newLink", $locationFullLink = "fullLink")
	{
		$this->locationList = $locationList;
		$this->locationNewLink = $locationNewLink;
		$this->locationFullLink = $locationFullLink;

		parent::__construct(User::ACCESSLEVEL_ALWAYS);
		
		$this->makeList();
		$this->makeNewLink();
	}
	
	protected function makeNewLink()
	{
		$b = new ActionButton("Nieuw Agenda Punt", $this->lifeId, "add");
		Response::field($this->locationNewLink, $b);
	}
	
	protected function makeList()
	{
		$sectionId = $this->sectionId;
		$full = $this->showFullList;
		
		$q = array(
			"select" => array("clID", "clTitle", "clText", "clTime"),
			"from" => array("site_calendar"),
			"order" => "clTime"
		);
		
		if(!$full) $q["limit"] = 8;
		
		list($data, $rest) = MySql::limitedSelect($q);
				
		$data->nbspForEmpty();
		$data->trunicate("clTitle", "clText");
		
		$data->addColumns("info");
		
		foreach($data as $rowId => $item)
		{
			if((int) $item["clTime"] <= time() - 24 * 3600)
			{
				$data->update("<em>Verouderd</em>", "info", $rowId);
			}
		}
		
		$data->date("clTime");
		
		$this->setData($data);
		$this->setIdField("clID");
		$this->setHeaders(array("info" => "", "clTitle" => "Titel", "clText" => "Omschrijving", "clTime" => "Datum"));
		$this->onClickPage("CalendarEditPage");
		$this->addDeleteField("dit agendapunt");
		
		$this->createList($this->locationList);
		
		$button = new FullListButton($rest, $this->lifeId);
		Response::field($this->locationFullLink, $button);
	}
	
	public function showFullList()
	{
		$this->showFullList = true;
		$this->makeList();
		Response::msg("De lijst wordt nu geheel weergegeven");
	}
	
	public function showSmallList()
	{
		$this->showFullList = false;
		$this->makeList();
		Response::msg("De lijst is nu ingeklapt");
	}
	
	public function add()
	{
		$n = MySql::insert(array(
			"table" => "site_calendar",
			"values" => array(
				"clTitle" => "Nieuw Agendapunt"
			)
		));
		
		Response::evalJS(new JSPage("CalendarEditPage", array("id" => $n, "firstEdit" => true)));
		
		Response::msg("Nieuw agendapunt aangemaakt, bewerken gestart...");
	
	}
	
	public function delete()
	{
		$deleteId = Request::$Post["id"];
		
		if(!$deleteId){
			trigger_error("Geen deleteId opgestuurd", e);
		}
		
		MySql::delete(array(
			"table" => "site_calendar",
			"where" => "clID = '$deleteId'",
			"limit" => "1"
		));
		
		Response::msg("Item werd succesvol verwijderd.");
		
		$this->makeList();
	}
}