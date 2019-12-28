<?php

abstract class Module_TwoColumn_Page extends Module_Basic_Page
{
	protected $textColumnLeft = "Deze afdeling";
	protected $textColumnRight = "Onderlinggende afdelingen";
	protected $widthColumnLeft;
	protected $widthColumnRight;

	protected function buildTemplate()
	{
		$Template = new Template("columns", array(
			"columnLeft" => $this->getEdit(),
			"columnRight" => $this->getChildren(),
			"widthLeft" => $this->widthColumnLeft,
			"widthRight" => $this->widthColumnRight,
		));
		
		Response::template((string) $Template);
	}
	
	protected function makeEdit()
	{	
		$content = "";
		$content .= new Template("bar", array("title" => $this->textColumnLeft));
		$content .= $this->Core->Edit->getEditables();
		
		return $content;
	}
	
	protected function makeChildren()
	{
		$content = "";
		$content .= new Template("bar", array(
			"title" => $this->textColumnRight,
			"right" => $this->Core->Children->getCreateInput()
		));
		$content .= $this->Core->Children->getTable();
		return $content;
	}
}