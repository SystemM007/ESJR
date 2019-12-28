<?php

class Function_LinkList extends Function_Abstract
{
	protected function startFunction()
	{
		if(Settings::get("ajaxLinks"))
		{
			throw new Exception("Nog niet gemaakt");

//			$data = MySql::select(array(
//				"select" => array("secID", "secTitle", "secUrlName"),
//				"from" => array("sys_sections"),
//				"order" => "secTitle",
//				"where" => "secSiteModule != ''"
//			));
//			
//
//			$data->addColumns("rel");
//			
//			foreach($data as $rowId => $row)
//			{				
//				if((bool) $row["secAjaxLoadable"]) $rel = "ajaxLoadable";
//				else $rel = "";
//				
//				$data->update($rel, "rel", $rowId);
//			}

		}
		else
		{
			$data = MySql::select(array(
				"select" => array("u_cores.ID", "u_cores.name", "u_sections.urlName", "u_sections.rel"),
				"from" => array("u_sections"),
				"join" => array("table" => "u_cores", "using" => "ID"),
				//"order" => "u_cores.name",
				"where" => "u_sections.linkable = '1'",
				"order" => "u_cores.name"
			));
		}
	
		
		foreach($data as $rowId => $row)
		{
			$Section = new Section($row["ID"]);
			$data->update($Section->getFullPath(), "urlName", $rowId);
		}
					
		$linkList = array();
		
		foreach($data as $val)
		{
			$linkList[] = array($val["name"], $val["urlName"], $val["rel"]);
		}
		
		echo "var tinyMCELinkList = ". json_encode($linkList) . "" ;
		
		exit;
	}
}
	