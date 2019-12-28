<?php

class TotalSectionCleanup
{
	public function __construct(Core $Root)
	{
		$this->scanAndRemove($Root->ID, false);
	}
	
	protected function scanAndRemove($ID, $delete = true)
	{
		$children = MySql::select(array(
			"select" => "ID",
			"from" => "u_cores",
			"where" => "parent = '$ID'"
		));
		
		foreach($children as $child) $this->scanAndRemove($child["ID"]);
		
		if($delete)
		{
			$Core = new Core($ID);
			$Core->Constructor->delete();
		}
	}
}