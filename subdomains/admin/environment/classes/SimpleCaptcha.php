<?php
/*****************************************************************
   Projectname:   Simple CAPTCHA class
   Version:       0.1 + LvdG
   Author:        Ver Pangonilo <smp@limbofreak.com>
   					Leon van der Graaff <leon@jongdesigns.nl>
   Last modified: 18 May 2006
   					3 March 2007 LvdG
   Copyright (C): 2006 Ver Pangonilo, All Rights Reserved
   

   * GNU General Public License (Version 2, June 1991)
   *
   * This program is free software; you can redistribute
   * it and/or modify it under the terms of the GNU
   * General Public License as published by the Free
   * Software Foundation; either version 2 of the License,
   * or (at your option) any later version.
   *
   * This program is distributed in the hope that it will
   * be useful, but WITHOUT ANY WARRANTY; without even the
   * implied warranty of MERCHANTABILITY or FITNESS FOR A
   * PARTICULAR PURPOSE. See the GNU General Public License
   * for more details.

   Description:
   This class can generate CAPTCHAs for user forms.
   
   
   // Edit By LvdG
   -  Letterspacing Added
   

 ******************************************************************/

class SimpleCaptcha {

	function SimpleCaptcha( $params = null ){
		$this->BackgroundImage = $params['BackgroundImage']; //background image
		$this->BackgroundColor = $params['BackgroundColor'];
		$this->Height = $params['Height']; //image height
		$this->Width = $params['Width']; //image width
		$this->FontSize = $params['Font_Size']; //text font size
		$this->Font = $params['Font']; //text font style
		$this->TextMinimumAngle = $params['TextMinimumAngle'];
		$this->TextMaximumAngle = $params['TextMaximumAngle'];
		$this->TextLetterSpacing = $params['TextLetterSpacing'];
		$this->TextColor = $params['TextColor'];
		$this->TextLength = $params['TextLength'];
		$this->Transparency = $params['Transparency'];
		
		$this->generateCode();
				
		$this->generateImage($this->Code);
	}
	//Background Images
	function getBackgroundImage()
	{
	   return $this->BackgroundImage;
	}
	
	function setBackgroundImage( $background_image = null )
	{
	   $this->BackgroundImage = $background_image;
	}
	
	//Backgroung Color
	function getBackgroundColor()
	{
		return $this->BackgroundColor;
	}
	
	function setBackgroundColor( $background_color )
	{
	   $this->BackgroundColor = $background_color;
	
	}
	
	//Image Height
	function getHeight()
	{
	   return $this->Height;
	}
	
	function setHeight( $height = null )
	{
	   $this->Height = $height;
	}
	//Image Width
	function getWidth()
	{
	   return $this->Width;
	}
	
	function setWidth( $width = null )
	{
	   $this->Width = $width;
	}
	//Font size
	function getFontSize()
	{
	   return $this->FontSize;
	}
	
	function setFontSize( $size = null )
	{
	   $this->FontSize = $size;
	}
	
	//Font
	function getFont()
	{
	   return $this->Font;
	}
	
	function setFont( $font = null )
	{
	   $this->Font = $font;
	}
	
	//Text Minimum Angle
	function getTextMinimumAngle()
	{
	   return $this->TextMinimumAngle;
	}
	
	function setTextMinimumAngle( $minimum_angle = null )
	{
	   $this->TextMinimumAngle = $minimum_angle;
	}
	
	//Text Maximum Angle
	function getTextMaximumAngle()
	{
	   return $this->TextMaximumAngle;
	}
	
	function setTextMaximumAngle( $maximum_angle = null )
	{
	   $this->TextMaximumAngle = $maximum_angle;
	}
	
	//Text Letterspacing
	function getTextLetterSpacing()
	{
	   return $this->TextLetterSpacing;
	}
	
	function setTextLetterSpacing( $space = null )
	{
	   $this->TextLetterSpacing = $space;
	}
	
	//Text Color
	function getTextColor()
	{
	   return $this->TextColor;
	}
	
	function setTextColor( $text_color )
	{
	   $this->TextColor = $text_color;
	}
	
	//Text Length
	function getTextLength()
	{
	   return $this->TextLength;
	}
	
	function setTextLength( $text_length = null )
	{
	   $this->TextLength = $text_length;
	}
	
	//Transparency
	function getTransparency()
	{
	   return $this->Transparency;
	}
	
	function setTransparency( $transparency = null )
	{
	   $this->Transparency = $transparency;
	}
	
	//get Captcha Code
	function getCode()
	{
		return $this->Code;
	}
	
	//Generate Captcha
	function generateCode(){

		$length = $this->getTextLength();

		$this->Code = "";

		/*while(strlen($this->Code)<$length){
			mt_srand((double)microtime()*1000000);
			$random=mt_rand(48,122);
			$random=md5($random);
			$this->Code .= substr($random, 17, 1);
		}*/
		/*
			length = 3 -> 10^(3-1) ~ 10^3 - 1
		*/
		$this->Code = (string) mt_rand(pow(10, $length-1),pow(10,$length)-1);
		
		return $this->Code;
	}
	
	
	function generateImage($text = null){
		$im = imagecreatefrompng( $this->getBackgroundImage() );
		$tColor = $this->getTextColor();
		$txcolor = $this->colorDecode($tColor);
		$bcolor = $this->getBackgroundColor();
		$bgcolor = $this->colorDecode($bcolor);
		$width = $this->getWidth();
		$height = $this->getHeight();
		$transprency = $this->getTransparency();
		$this->im = imagecreate($width,$height);
		$imgColor = imagecolorallocate($this->im, $bgcolor[red], $bgcolor[green], $bgcolor[blue]);
		imagecopymerge($this->im,$im,0,0,0,0,$width,$height,$transprency);
		$textcolor = imagecolorallocate($this->im, $txcolor[red], $txcolor[green], $txcolor[blue]);
		$font = $this->getFont();
		$fontsize=$this->getFontSize();
		$minAngle = $this->getTextMinimumAngle();
		$maxAngle = $this->getTextMaximumAngle();
		$length = $this->getTextLength();
	
		for($i=0;$i<$length;$i++){
			imagettftext(
						 $this->im, 					// afbeelding
						 $fontsize,						// lettergrootte
						 rand(-$minAngle,$maxAngle), 	// hoek waaronder de letter staat
						 $i*$this->getTextLetterSpacing()+10,						// x-positie:  linkeronderhoek letter
						 $this->FontSize*1.2,			// y-positie: de 'baseline'
						 $textcolor,					// tekst kleur
						 $font,							// fontfile
						 substr($text, $i, 1));			// de tekst
		}
		
	
		header("Content-type: image/png");
		imagepng($this->im);
		imagedestroy($this->im);
	}
	
	function colorDecode( $hex ){
	
	   if(!isset($hex)) return FALSE;
	
	   $decoded[red] = hexdec(substr($hex, 0 ,2));
	   $decoded[green] = hexdec(substr($hex, 2 ,2));
	   $decoded[blue] = hexdec(substr($hex, 4 ,2));
	
	   return $decoded;
	
	}


}

?>
