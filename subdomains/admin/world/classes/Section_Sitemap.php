<?php
class Section_Sitemap extends Section_Abstract
{
	protected $elements;
	
	public function __construct($ID)
	{
		$this->elements = new Matrix(false, array("url", "lastmod", "priority"));
		
		$parentID = MySql::selectValue(array(
			"select" => "parent",
			"from" => "u_cores",
			"where" => "ID = '$ID'"
		));
		
		$url = $this->getUrl($parentID);
		
		$this->map($parentID, $url);
	}
	
	protected function getUrl($ID)
	{	
		$url = "";
		do
		{
			$section = MySql::selectRow(array(
				"select" => array("u_sections.urlName", "u_cores.parent"),
				"from" => "u_sections",
				"join" => array(
					"u_cores",
					array("table" => "u_cores AS childTable", "on" => "childTable.parent = u_sections.ID"),
				),
				"where" => "childTable.ID = '$ID'",
			));
			
			if(strlen($section["urlName"]))
			{
				$url = $section["urlName"] . "/" . $url;
			}
		}
		while($section["parent"]);
		
		$url = "http://www." . Uri::abs_host . "/" . $url;
		
		return $url;
	}
	
	protected function map($ID, $url)
	{
		$section = MySql::selectRow(array(
			"select" => array(
				"u_cores.childrenAllowed", 
				"u_sections.linkAble",
				"u_sections.urlName", "u_sections.priority", "DATE(u_sections.lastmod) AS lastmod"
			),
			"from" => "u_sections",
			"join" => "u_cores",
			"where" => "u_sections.ID = '$ID' AND u_cores.enabled = '1'"
		));
		
		if(strlen($section["urlName"]))
		{
			$url .= $section["urlName"] . "/";
		}
		
		if((int) $section["linkAble"])
		{
			/*
			* deze controle is nodig omdat sommige secties NIET linkable zijn maar
			* wel children bevatten die dat zijn
			*/
			$this->elements->addRow(array(
				"url" => $url,
				"lastmod" => $section["lastmod"],
				"priority" => $section["priority"],
			));
		}
		
		if($section["childrenAllowed"])
		{
			$children = MySql::select(array(
				"select" => "u_sections.ID",
				"from" => "u_sections",
				"join" => "u_cores",
				"where" => "u_cores.parent = '$ID'",
			));
			
			foreach($children as $child)
			{
				$this->map($child["ID"], $url);			
			}
		}
	}
	
	public function finish()
	{
		$Template = new Template("sitemap_sitemap", array(
			"items" => $this->elements,
		));
		
		header("Content-Type:text/xml");
		echo $Template;
	}
}