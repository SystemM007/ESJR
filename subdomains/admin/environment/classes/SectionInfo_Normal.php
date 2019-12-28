<?php
class SectionInfo_Normal extends SectionInfo_Abstract
{
	protected $Parent;
	
	public function getFullPath()
	{
		return $this->Parent->getFullPath() . $this->data["urlName"] . "/";
	}
}
?>