<?php
abstract class SectionInfo_Abstract
{
	protected $data;
	protected $children = array();
	protected $Parent;
	
	public function __construct(array $data, array $childData, SectionInfo_Abstract $Parent = NULL)
	{
		$this->data = $data;
		$this->parent = $Parent;
		
		foreach($childData as $child)
		{
			$this->children[] = new SectionInfo_Normal($child["data"], $child["children"], $this);
		}
		
		SectionInfo::add($data["ID"], $this);
	}
	
	abstract public function getFullPath();
	
	public function __get($key)
	{
		if(! isset($this->data[$key]) ) throw new Exception("Tried to excess undefined property '$key'");
		return $this->data[$key];
	}
	
	public function getParent()
	{
		return $this->Parent;
	}
	
	public function getChildren()
	{
		return $this->children;
	}
	
	// wie weet leuk nog een keer een __set te klussen?
	
	public function __toString()
	{
		return $this->getFullPath();
	}
}
?>