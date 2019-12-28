<?

class Section_BlogItem extends Section_WebPage 
{

	public function __construct($sectionId)
	{
		parent::__construct($sectionId);
		
		$this->Template->fuse("left", new Template("blog-left"));
		$this->Template->fuse("text", new Template("blog"));
		
		$this->Template->fill("leftimages", new Fragment_PageImages($this));
		
		$this->blog();
		$this->otherblogs();
	}
	
	public function blog()
	{
		$data = MySql::selectRow("
			SELECT 
				author, introduction, DATE_FORMAT(date,'%d-%m-%Y') as date
			FROM w_blog
			WHERE ID = '$this->ID'
		");
		
		$this->Template->fill($data);
	}
	
	public function otherblogs()
	{
		$data = MySql::select("
			SELECT 
				name, urlName
			FROM w_blog
			LEFT JOIN u_cores USING(ID)
			LEFT JOIN u_sections USING(ID)
			WHERE ID != '$this->ID' AND u_cores.enabled = '1'
			ORDER BY u_cores.order
			LIMIT 6
		");
		
		$this->Template->fill("otherblogs", "items", $data);
	}
}