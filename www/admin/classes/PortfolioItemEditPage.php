<?php

class PortfolioItemEditPage extends UnwrittenPage
{

	protected $historyName = "Portfolio item";

	private $editorLifeId;
	private $itemId;
	
	public function __construct()
	{
		$this->itemId = Request::$Post["id"];
		
		$title = MySql::selectValue(array(
			"select" => "pfTitle",
			"from" => "spec_portfolio",
			"where" => "pfID = '$this->itemId'"
		));
		
		$this->historyName = $title;
				
		parent::__construct(User::ACCESSLEVEL_ALWAYS);
		
		Response::title("Bewerk de gegevens voor item '$title'");
		
		Response::template(new Img(Uri::portfolioImg . "thumb/" . $this->itemId . ".jpg"));
				
		$Name = new TextEditable("Naam", array("spec_portfolio", "pfID", $this->itemId, "pfTitle")); 
		$Name->checkNonEmpty();
		$this->registerSaveable($Name);
		Response::template($Name);
		
		$Text = new TinyEditable("Omschrijving", array("spec_portfolio", "pfID", $this->itemId, "pfText")); 
		$this->registerSaveable($Text);
		Response::template($Text);
		
		$Time = new DateEditable("Datum", array("spec_portfolio", "pfID", $this->itemId, "pfTime")); 
		$Time->checkNonEmpty();
		$this->registerSaveable($Time);
		Response::template($Time);
		
		Response::buttons(
			new HistoryJumpButton() . 
			new SaveButton($this->lifeId) .
			new ResetButton()
		);
	}
}