<?php


class ImageJpeg {
	
	var $image = '';
	var $temp = '';
	var $quality = 75;
	
	function __construct($sourceFile)
	{	
		if(!file_exists($sourceFile))
		{
			trigger_error("Bestand '$sourceFile' niet gevonden!", e);
		}
		$this->image = ImageCreateFromJPEG($sourceFile);
	}
	
	function resize($width = 100, $height = 100, $aspectradio = true){ // dit is in procenten
		$o_wd = imagesx($this->image);
		$o_ht = imagesy($this->image);
		if(isset($aspectradio)&&$aspectradio) {
			$w = round($o_wd * $height / $o_ht);
			$h = round($o_ht * $width / $o_wd);
			if(($height-$h)<($width-$w)){
				$width =& $w;
			} else {
				$height =& $h;
			}
		}
		$this->temp = imageCreateTrueColor($width,$height);
		imageCopyResampled($this->temp, $this->image,
		0, 0, 0, 0, $width, $height, $o_wd, $o_ht);
		$this->sync();
		return;
	}
	
	function resize_max_px($size)
	{ 
		// LvdG versie: maximaal aantal pixels (zowel hoogte als breedte)
		// Get new dimensions
		$currentWidth = imagesx($this->image) ;
		$currentHeigth = imagesy($this->image) ;
		
		if($currentWidth < $size && $currentHeigth < $size)
		{	// niet verkleinen als plaatje binnen size valt
			return true;
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
		
		// Resample
		$this->temp = @imagecreatetruecolor((int) $width, (int) $height);
		if(!$this->temp) throw new Exception("Kon geen afbeelding makem net h:$height, w:$widht");
		$R = imagecopyresampled($this->temp, $this->image, 0, 0, 0, 0, $width, $height, $currentWidth, $currentHeigth);
		$this->sync();
		return $R;
	}
	
	function sync(){
		$this->image =& $this->temp;
		unset($this->temp);
		$this->temp = '';
		return;
	}
	
	function show(){
		$this->_sendHeader();
		$R = ImageJPEG($this->image);
		return $R;
	}
	
	function _sendHeader(){
		header('Content-Type: image/jpeg');
	}
	
	function errorHandler($msg){
		echo "$msg";
		exit();
	}
	
	function store($file){
		$R = ImageJPEG($this->image, $file, $this->quality);
		return $R;
	}
	
	function watermark($pngImage, $left = 0, $top = 0){
		ImageAlphaBlending($this->image, true);
		$layer = ImageCreateFromPNG($pngImage); 
		$logoW = ImageSX($layer); 
		$logoH = ImageSY($layer); 
		ImageCopy($this->image, $layer, $left, $top, 0, 0, $logoW, $logoH); 
	}
	
	function destroy(){
		return ImageDestroy($this->image);
	}
}

?>