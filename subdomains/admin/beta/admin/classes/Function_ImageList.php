<?php
class Function_ImageList extends Function_Abstract
{
	protected function startFunction()
	{
		$data = MySql::select(array(
			"select" => array("u_cores.ID", "u_cores.name",),
			"from" => "u_images",
			"join" => array("table" => "u_cores", "using" => "ID"),
			"where" => "parent = '" . Settings::get("imageContainerID") . "'",
			"order" => "name"
		));
		
		$data->wrap(Uri::textimages . "image/", ".jpg", "ID");
		
		$imageList = array();
		
		foreach($data as $val)
		{
			$imageList[] = array($val["name"], $val["ID"]);
		}
		
		echo "var tinyMCEImageList = ". json_encode($imageList) . "" ;
		
		exit;
	}
}
	