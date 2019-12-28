<?php

class Module_Image_Edit extends Module_Basic_Edit
{
	private $LocationPreview;
	
	public function isRequestable($function)
	{
		return in_array($function, array("resize")) || parent::isRequestable($function);
	}

	protected function makeEditables()
	{	
		$editables = "";
		
		$editables .= $this->coreName("Afbeelding naam");
		
		$editables .= $this->imageScale();
		
		//if(User::levelAllowed(User::ACCESSLEVEL_HIGHEST)) // sowieso niet te bedoeling!
		//	$editables .= $this->coreModule();
			
		//if(User::levelAllowed(User::ACCESSLEVEL_NORMAL)) // niet voor easy
		//	$editables .= $this->coreReadLevel();
		
		//if(User::levelAllowed(User::ACCESSLEVEL_NORMAL)) // niet voor easy
		//	$editables .= $this->coreWriteLevel();
		
		return $editables;
	}
	
	protected function imageScale()
	{
		$sizes = array();
			
		$Img = ImageCreateFromJPEG(Dir::textimages . "full/" . $this->Core->ID . ".jpg");
		
		$maxDimention = max(imagesx($Img), imagesy($Img));
		$maxImgSize = Settings::get("textImageMaxSize");
		
		$maxScale = min($maxDimention, $maxImgSize);
				
		for($n = 50; $n <= $maxScale; $n += 50)
		{
			$sizes[] = array("id" => $n, "name" => "$n px");		
		}
		$select = new Fragment_Select_Action(new Matrix($sizes), "id", "name", $this->getLifeId(), "resize");
		
		$edit = "<h2>Afbeelding schalen</h2><p>$select</p>";
		$edit .= $this->LocationPreview = new Location($this->makePreview());
		
		return $edit;
	}
	
	protected function makePreview()
	{
		return (string) new Fragment_Tag_Img(Uri::textimages . "image/" . $this->Core->ID . ".jpg?rand=" . uniqid());
	}
	
	public function resize()
	{
		$maxSize = Request::$Post["id"];

		$Img = new ImageJpeg(Dir::textimages . "full/" .  $this->Core->ID . ".jpg");
		$Img->resize_max_px($maxSize);
		
		$Img->store(Dir::textimages . "image/" .  $this->Core->ID . ".jpg");
		
		Response::msg("Afbeelding is succesvol geschaalt");
		
		$this->LocationPreview->update($this->makePreview());
	}
}