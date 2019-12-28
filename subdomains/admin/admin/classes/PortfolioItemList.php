<?php

class PortfolioItemList extends ListModule
{	
	private $locationList;
	private $locationUpload;
	private $locationFullLink;
	
	private $collectionId;
	

	public function __construct($locationList = "list", $locationUpload = "upload", $locationFullLink = "fullLink")
	{
	
		$this->locationList = $locationList;
		$this->locationUpload = $locationUpload;
		$this->locationFullLink = $locationFullLink;

		parent::__construct(User::ACCESSLEVEL_ALWAYS);
		
		$this->makeList();
		$this->makeUpload();
	}
	
	protected function makeUpload()
	{
		$upload = new Upload($this->lifeId, "upload");
		Response::field($this->locationUpload, $upload);
	}
	
	protected function makeList()
	{
		$collectionId = $this->collectionId;
		
		$q = array(
			"select" => array("pfID", "pfTitle", "pfText", "pfTime"),
			"from" => array("spec_portfolio"),
			"order" => "pfTime DESC"
		);

		$data = MySql::select($q);
		
		$data->cloneColumn("pfID", "thumb");
		$data->wrap("<img src='". Uri::portfolioImg . "thumb/", ".jpg' alt='' />", "thumb");
		
		$data->date("pfTime");
		$data->nbspForEmpty();
		$data->trunicate("pfTitle", "pfText");
		
		$this->setData($data);
		$this->setIdField("pfID");
		$this->setHeaders(array("thumb" => "Afbeelding", "pfTitle" => "Naam", "pfTime" => "Datum", "pfText" => "Omschrijving"));
		$this->onClickPage("PortfolioItemEditPage");
		$this->addDeleteField("dit onderdeel");
		
		$this->createList($this->locationList);
	}
	
	public function upload()
	{
		if(!Request::$File) trigger_error("Er is geen upload gevonden!", e);

		$expander = new ExpandUpload(array("jpg", "jpeg"));
		$fileList = $expander->getFileList();
		$imageWriter = new ImageWriter($fileList);
		
		$imageWriter->toDatabase("spec_portfolio", false, "pfTitle", "pfTime");
		
		$imageWriter->toFile(Dir::portfolioImg . "image/", 350, 80);
		$imageWriter->toFile(Dir::portfolioImg . "thumb/", 120, 70);
		$imageWriter->toFile(Dir::portfolioImg . "full/");
				
		switch($numFiles = $fileList->count())
		{
			case 0:
				Response::tip("<h2>Upload fout</h2><p>Er zijn geen bestanden toegevoegd.</p><p>Verzend of jpg bestanden of zip bestanden die jpg's bevatten!</p>");
				Response::msg("Upload Fout!");
				return;
			break;
			
			case 1:
				$b = "Het bestand is";
			default:
				$b = $b ? $b : "$numFiles bestanden zijn";
				Response::tip("<h2>Upload Succesvol</h2><p>$b toegevoegd.</p><p>U kunt meer afbeeldingen inladen of een van de ingevoegde items bewerken.</p>");
		}
		
		Response::msg("Upload voltooid");
		
		$this->makeList();
		
	}
	
	public function delete()
	{
		$deleteId = Request::$Post["id"];
		
		if(!$deleteId){
			trigger_error("Geen deleteId opgestuurd", e);
		}
		
		MySql::delete(array(
			"table" => "spec_portfolio",
			"where" => "pfID = $deleteId",
			"limit" => "1"
		));
		
		//$a = array(Dir::portfolioImg . "image/", Dir::portfolioImg . "thumb/", Dir::portfolioImg . "full/");
		$a = array(Dir::portfolioImg . "image/", Dir::portfolioImg . "thumb/");
				
		foreach($a as $dir){
			$file = $dir . "$deleteId.jpg";
			if(file_exists($file)) @unlink($file);
			else Response::msg("'$file' werd niet gevonden. ");
		}
		Response::msg("Item werd succesvol verwijderd.");
		
		$this->makeList();
	}
}