<?php

class Upload_Jpeg extends Upload_Basic
{
	protected function onConstruct()
	{
		$this->addFileExtentions(array("jpg", "jpeg"));
		$this->addMimeTypes("image/jpeg");
		$this->setFileTypesDescription("Selecteer jpg afbeeldingen");
	}
}