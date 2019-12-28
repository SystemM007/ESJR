<?

class Section_Workgroup extends Section_WebPage 
{

	public function __construct($sectionId)
	{
		parent::__construct($sectionId);
		
		$this->Template->fuse("left", new Template("workgroup-left"));
		$this->Template->fuse("text", new Template("workgroup"));

		$this->Template->fill("leftimages", new Fragment_PageImages($this));
				
		$this->workgroup();
		$this->otherworkgroups();
	}
	
	public function workgroup()
	{
		$data = MySql::selectRow("
			SELECT 
				description
			FROM w_workgroup
			WHERE ID = '$this->ID'
		");
		
		$this->Template->fill($data);
	}
	
	public function otherworkgroups()
	{
		$data = MySql::select("
			SELECT 
				name, urlName
			FROM w_workgroup
			LEFT JOIN u_cores USING(ID)
			LEFT JOIN u_sections USING(ID)
			WHERE ID != '$this->ID' AND u_cores.enabled = '1'
			ORDER BY u_cores.order
		");
		
		$this->Template->fill("otherworkgroups", "items", $data);
	}
}