<?

class Section_Calendar extends Section_WebPage 
{

	public function __construct($sectionId)
	{
		parent::__construct($sectionId);
		
		$data = MySql::select("
			SELECT ID, name, text, date 
			FROM w_calendar 
			LEFT JOIN u_cores USING(ID)
			WHERE date >= CURRENT_DATE()
			ORDER BY w_calendar.date
		");
		
		$data->date("date");
		
		$this->Template->fill("rest", new Template("calendar", array("items" => $data)));
		
		$this->Template->fill("left", new Fragment_PageImages($this));
	}
}