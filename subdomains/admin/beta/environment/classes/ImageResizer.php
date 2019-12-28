<?php


class ImageResizer
{
	private $Original;
	private $source;

	public function __construct($jpegFile)
	{
		if(!file_exists($jpegFile)) throw new Exception("Bestand '$jpegFile' niet gevonden!", E_USER_ERROR);
		
		$this->source = $jpegFile;
		
		$this->Original = @imagecreatefromjpeg($jpegFile);
		
		if(!$this->Original) throw new Exception("Kon niet lezen van bestand '$jpegFile'", E_USER_ERROR);
	}
	/*
	 * Als size een integer is, dan is dat de maximum waarde en wordt de afbeelding 
	 * geschaald
	 * Als size een array is, is de eerste waarde de breedte en de tweede de hoogte
	 */
	public function storeResized($fileName, $size = NULL, $quality = 75)	
	{ 
		if(!$size)
		{	
			copy($this->source, $fileName);
			return;
		}
		elseif(is_array($size))
		{
			list($width, $height, $currentWidth, $currentHeigth) = $this->getSizes($size);
		}
		else
		{
			list($width, $height, $currentWidth, $currentHeigth) = $this->sizesFromMax($size);
		}
		
		$tmp = $this->resizePx($width, $height, $currentWidth, $currentHeigth);
		
		$this->toFile($tmp, $fileName, $quality);
	}
	
	private function sizesFromMax($size)
	{
		$currentWidth = imagesx($this->Original) ;
		$currentHeigth = imagesy($this->Original) ;
		
		if($currentWidth < $size && $currentHeigth < $size)
		{	// niet verkleinen als plaatje binnen size valt
			return array($currentWidth, $currentHeigth, $currentWidth, $currentHeigth);
		}
		
		if ($currentHeigth >= $currentWidth)
		{
			$height = $size;
			$width = round( ($height / $currentHeigth) * $currentWidth );
		}
		else
		{
			$width = $size;
			$height = round( ($width / $currentWidth) * $currentHeigth ) ;
		}
		
		return array($width, $height, $currentWidth, $currentHeigth);
	}
	
	private function getSizes(array $size)
	{
		$currentWidth = imagesx($this->Original) ;
		$currentHeigth = imagesy($this->Original) ;
		$width = $size[0];
		$height = $size[1];
		
		return array($width, $height, $currentWidth, $currentHeigth);
	}
	
	private function resizePx($width, $height, $currentWidth, $currentHeigth)
	{
		if(!$width || !$height) throw new Exception("h:'$height', w:'$width'");
		
		$temp = @imagecreatetruecolor((int) $width, (int) $height);
		if(!$temp) throw new Exception("Kon geen afbeelding makem net h:'$height', w:'$width'");
		imagecopyresampled($temp, $this->Original, 0, 0, 0, 0, $width, $height, $currentWidth, $currentHeigth);
		
		return $temp;	
	}
	
	private function toFile($imgResource, $fileName, $quality = 75)
	{
		$error = !imagejpeg($imgResource, $fileName, $quality);
		if($error) throw new Exception();
	}	
}

?>