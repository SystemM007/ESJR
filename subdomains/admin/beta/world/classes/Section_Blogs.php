<?

class Section_Blogs extends Section_WebPage 
{

	public function __construct($sectionId)
	{
		parent::__construct($sectionId);
		
		$this->Template->fuse("rest", new Template("blogs"));

		$this->blogs();
	}	
	
	
	public function blogs()
	{
		$data = MySql::select("
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
		");
		
		$this->Template->fill("blogs", $data);
	}
}