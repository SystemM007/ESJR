<?

class Section_Workgroups extends Section_WebPage 
{

	public function __construct($sectionId)
	{
		parent::__construct($sectionId);
		
		$this->Template->fuse("rest", new Template("workgroups"));

		$this->workgroups();
	}	
	
	
	public function workgroups()
	{
		$data = MySql::select("
			SELECT 
				title, urlName,
				description,
				(
					SELECT u_cores.ID FROM u_cores WHERE parent = w_workgroup.id ORDER BY u_cores.order LIMIT 1
				) AS image
			FROM w_workgroup
			LEFT JOIN u_cores USING(ID)
			LEFT JOIN u_sections USING(ID)
			LEFT JOIN u_webpages USING(ID)
			WHERE u_cores.enabled = '1'
			ORDER BY u_cores.order
		");
		
		$this->Template->fill("workgroups", $data);
	}
}