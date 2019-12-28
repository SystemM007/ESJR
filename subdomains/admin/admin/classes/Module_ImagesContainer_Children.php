<?php

class Module_ImagesContainer_Children extends Module_Basic_Children
{
	protected function listOrder($order = "")
	{
		return "u_cores.ID DESC";
	}

	protected function makeCreateInput()
	{
		$Upload = new Upload_Jpeg("Afbeelding toevoegen", array($this, "upload"));
		$Upload->setMultipleFiles(true);
		return (string) $Upload;
	}
	
	protected function processData($data)
	{
		$data->cloneColumn("ID", "thumb");
		$data->wrap("<img src='" . Uri::textimages . "thumb/", ".jpg' alt='' />", "thumb");
		return parent::processData($data);
	}
	
	protected function headers(array $headers = array())
	{
		$headers = array_merge(array(
			"thumb" => "",
		),$headers);
		return parent::headers($headers);
	}
	
	public function childTypeIsAllowed($module)
	{
		return ($module == "Image") ;
	}
	
	// callback voor upload!
	public function upload($File)
	{
		$Constructor = $this->getConstructor();
		$Constructor->create($this->Core, $File);
		
		// $this->refresh(); // << kan niet, te veel data, wat resulteert in problemen bij de flashplayer van Linux :S
		Response::msg("Afbeelding toegevoegd");
		Response::evalJs(new Fragment_JS_ServerAction(new ServerAction(array($this, "refresh")))); 
	}
	
	protected function getConstructor()
	{
		return new Module_Image_Constructor();
	}
}