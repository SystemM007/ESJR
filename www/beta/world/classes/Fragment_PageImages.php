<?php 

class Fragment_PageImages extends Fragment_Abstract
{
	protected $ID;
	
	public function __construct(Section_Abstract $Section)
	{
		$this->ID = $Section->getID();
	}
	
	public function create()
	{
		$Template = new Template("pageimages");
		$Template->fill("items", $this->getItems());
		
		return (string) $Template;
	}
	
	protected function getItems()
	{
		return MySql::select("
			SELECT ID AS image
			FROM u_cores 
			WHERE parent = '$this->ID' AND module = 'PageImage'
			ORDER BY u_cores.order
		");
	}
	
}