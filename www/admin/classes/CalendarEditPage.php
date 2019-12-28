<?php

class CalendarEditPage extends UnwrittenPage
{

	private $firstEdit = false;

	protected $historyName = "Kalender item";

	public function __construct()
	{
		if(Request::$Post["firstEdit"]) $this->firstEdit = true;
		
		$commId = Request::$Post["id"];

		parent::__construct(User::ACCESSLEVEL_ALWAYS);

		
		Response::title("Bewerk een agendapunt");
		Response::tip("<h2>Bericht bewerken</h2><p>U kunt nu deze pagina bewerken. Klik hiertoe op een van de lichtblauw omlijnde velden<p>");
		
		$Titel = new TextEditable("Titel", array("site_calendar", "clID", $commId, "clTitle"));
		$Titel->checkNonEmpty();
		if($this->firstEdit) $Titel->optionGiveFocus();
		$this->registerSaveable($Titel);
		
		$Date = new DateEditable("Datum", array("site_calendar", "clID", $commId, "clTime"));
		$Date->checkDateFuture();
		if($this->firstEdit) $Date->optionDirectConvert();
		$this->registerSaveable($Date);
		
		$Text = new TinyEditable("Text", array("site_calendar", "clID", $commId, "clText"));
		$this->registerSaveable($Text);
		
		Response::template(
			$Titel .
			$Date .
			$Text
		);
		
		Response::buttons(
			new HistoryJumpButton() .
			new SaveButton($this->lifeId) .
			new ResetButton()
		);
	}
}