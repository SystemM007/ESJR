<?

class Module_ImagePage_Constructor extends Module_WebPage_Constructor
{	
	protected function getNewChildrenAllowed()
	{
		return true;
	}
	
	protected function getNewEnabled()
	{
		return false;
	}
	
	protected function getNewSiteModule()
	{	
		return "ImagePage";
	}
}