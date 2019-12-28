<?php

class Function_Transfer_Page extends Function_Abstract
{
	protected $accessLevel = User::ACCESSLEVEL_HIGHEST;
	
	protected $oldParent = array();
	
	protected $newIDs = array();
	
	
	final protected function startFunction()
	{
	
		//MySql::echoQuery();
	
		$this->underHome();
		
		 $data = $this->selectOldData();
		 
		 foreach($data as $old) $this->transfer($old);
		 
		 $this->transferHome();
		 
		 $this->transferHierarchy();
	}
	
	protected function underHome()
	{
		$homeId = MySql::selectValue("SELECT secID FROM sys_sections WHERE secParent = '0' AND secUrlName = ''");
		
		MySql::query("UPDATE sys_sections SET secParent = '$homeId' WHERE secParent = '0' AND secUrlName != ''");
	}
	
	protected function selectOldData()
	{
		return MySql::select(
			"SELECT * FROM sys_sections LEFT JOIN site_textpages ON tpSection = secID WHERE secParent != '0'"
		);
	}
	
	protected function selectHomeData()
	{
		return MySql::selectRow("SELECT * FROM sys_sections LEFT JOIN site_textpages ON tpSection = secID WHERE secParent = '0'");
	}
	
	protected function transfer($old)
	{

		$ID = MySql::insert(array(
			"table" => "u_cores",
			"values" => array(
				"name" => $old["secTitle"],
				"module" => $old["tpID"] ? "WebPage" : "Section",
				"readLevel" => User::ACCESSLEVEL_SUPERUSER,
				"writeLevel" => User::ACCESSLEVEL_SUPERUSER,
				"childCreateLevel" => User::ACCESSLEVEL_SUPERUSER,
				"parent" => NULL,
				"childrenAllowed" => $old["childrenAllowed"],
			),
		));
		
		$this->oldParent[$ID] = $old["secParent"];
		$this->newIDs[$old["secID"]] = $ID;
		
		MySql::insert(array(
			"table" => "u_sections",
			"values" => array(
				"ID" => $ID,
				"urlName" => $old["secUrlName"],
				"siteModule" => $old["secSiteModule"],
				"paramAllowed" => false,
				"linkAble" => true,
				"rel" => NULL,
				"priority" => NULL,
				"lastmod" => NULL
			),
		));
		
		if($old["tpID"])
		{
			MySql::insert(array(
				"table" => "u_webpages",
				"values" => array(
					"ID" => $ID,
					"title" => $old["secTitle"],
					"text" => $old["tpContent"],
				),
			));
		}
		
		
		return $ID;
	}
	
	protected function transferHome()
	{
		$home = $this->selectHomeData();
		
		$homeID = Settings::get("homePageID");
		
		MySql::update(array(
			"table" => "u_webpages",
			"values" => array(
				"title" => $home["secTitle"],
				"text" => $home["tpContent"],
			),
			"where" => "ID = '$homeID'",
		));
		
		$this->newIDs[$home["secID"]] = $homeID;
	}
	
	
	protected function transferHierarchy()
	{
		foreach($this->oldParent as $ID => $oldParent)
		{
			$newParent = $this->newIDs[$oldParent];
			
			MySql::update(array(
				"table" => "u_cores",
				"where" => "ID = '$ID'",
				"values" => array("parent" => $newParent),
			));
		}
	}
}
			
			
	
	
	