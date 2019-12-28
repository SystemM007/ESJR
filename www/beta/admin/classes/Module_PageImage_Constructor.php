<?php
class Module_PageImage_Constructor extends Module_Image_Constructor
{
protected function writeImages(ImageResizer $Image, $name)
	{
		$Image->storeResized(Dir::pageimages . "image/$name", Settings::get("pageImageSize"), Settings::get("pageImageQuality"));
		$Image->storeResized(Dir::pageimages . "sitethumb/$name", Settings::get("pageImageThumbSize"), Settings::get("pageImageThumbQuality"));
		$Image->storeResized(Dir::pageimages . "thumb/$name", 120, 70);
		$Image->storeResized(Dir::pageimages . "full/$name");
	}
	
	protected function removeImages($name)
	{
		$this->saveUnlink(Dir::pageimages . "image/$name");
		$this->saveUnlink(Dir::pageimages . "thumb/$name");
		$this->saveUnlink(Dir::pageimages . "sitethumb/$name");
	}
}