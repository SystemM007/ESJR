<?

class Section_WebPage extends Section_Abstract_HTMLPage
{
	public function __construct($ID)
	{
		parent::__construct($ID);
		
		$this->createTemplate();
		
		$this->fillTextAndTitle();
	}
}