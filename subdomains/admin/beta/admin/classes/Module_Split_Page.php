<?php

abstract class Module_Split_Page extends Module_TwoColumn_Page
{
	/*
	 * titels zijn toch echt een page dingetje
	 */
	protected $titles = array();
	
	protected function makeChildren()
	{
		$Children = $this->Core->Children;
		if(! $Children instanceof Module_Split_Children) throw new Exception("Children of a Page wich is an instanceof Split_Page must be an instanceof Split_Children");
		
		$content = "";
		
		foreach($Children->getChildClasses() as $objectName => $className)
		{
			$content .= new Template("bar", array(
				"title" => isset($this->titles[$objectName]) ? $this->titles[$objectName] : "", 
				"right" => $this->Core->Children->$objectName->getCreateInput()
			));
			$content .= $this->Core->Children->$objectName->getTable();
		}
					
		return $content;
	}
}