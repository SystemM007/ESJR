<?

class Module_ImagePage_Page extends Module_Split_Page
{
	protected $textColumnLeft = "Deze pagina";
	
	protected $titles = array(
		"Subpages" => "Onderliggende pagina's",
		"Images" => "Afbeeldingen"
	);
	
	protected function onNoChildren(){}
	
	protected function onNoEdit(){}
}