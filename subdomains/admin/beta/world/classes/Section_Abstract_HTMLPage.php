<?
abstract class Section_Abstract_HTMLPage extends Section_Abstract
{
	protected $Template;
	
	protected $ID;
	
	protected $templatePath = "site";
	
	
	public function __construct($ID)
	{
		parent::__construct($ID);
	}
	
	protected function createTemplate()
	{
		$this->Template = new Template($this->templatePath);
	}
	
	protected function fillTitle()
	{
		$title = MySql::selectRow(array(
			"select" => "title",
			"from" => "u_webpages",
			"where" => "ID = '$this->ID'"
		));
		
		$this->Template->singleFill($title);
	}
	
	protected function fillTextAndTitle()
	{
		$pdata = MySql::selectRow(array(
			"select" => array("title", "text"),
			"from" => "u_webpages",
			"where" => "ID = '$this->ID'"
		));
		
		$this->Template->singleFill($pdata);
	}
	
	public function finish()
	{
		if($this->Template) echo $this->Template;
	}
}
