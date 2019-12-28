<?php
class Module_ImagePage_Children extends Module_ImagesContainer_Children
{
	protected $sortable = true;
	
	protected $displayCoreName = false;
	
	protected function listOrder()
	{
		return "u_cores.order";
	}
	
	protected function listWhere($where = "")
	{
		return "AND u_cores.module = 'PageImage' " . $where;
	}
	
	protected function getClickCoreRows()
	{
		return array();
	}
	
	protected function getConstructor()
	{
		return new Module_PageImage_Constructor();
	}
	
	public function childTypeIsAllowed($module)
	{
		return ($module == "PageImage") ;
	}
	
	protected function processData($data)
	{
		$data->cloneColumn("ID", "thumb");
		$data->wrap("<img src='" . Uri::pageimages . "thumb/", ".jpg' alt='' />", "thumb");
		return $data;
	}
}