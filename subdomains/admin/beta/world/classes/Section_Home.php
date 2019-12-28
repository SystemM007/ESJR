<?

class Section_Home extends Section_WebPage 
{

	public function __construct($sectionId)
	{
		parent::__construct($sectionId);
		
		$this->Template->fuse("left", new Template("home-left"));
		$this->Template->fuse("rest", new Template("home-blog"));
		
		$this->calendar();
		$this->blog();
	}	
	
	
	public function calendar()
	{
		$data = MySql::select("
			SELECT ID, name, date 
			FROM w_calendar 
			LEFT JOIN u_cores USING(ID)
			WHERE date >= CURRENT_DATE()
			ORDER BY w_calendar.date
			
			LIMIT 5
		");
		
		$data->date("date");
		
		$this->Template->fill("agenda", "items", $data);
	}
	
	public function blog()
	{
		$data = MySql::selectRow("
			SELECT 
				title, urlName,
				author, DATE_FORMAT(date,'%d-%m-%Y') as date, introduction,
				(
					SELECT u_cores.ID FROM u_cores WHERE parent = w_blog.id ORDER BY u_cores.order LIMIT 1
				) AS image
			FROM w_blog
			LEFT JOIN u_cores USING(ID)
			LEFT JOIN u_sections USING(ID)
			LEFT JOIN u_webpages USING(ID)
			WHERE u_cores.enabled = '1'
			ORDER BY u_cores.order
			LIMIT 1
		");
		
		$this->Template->fill("blog", $data);
	}
}