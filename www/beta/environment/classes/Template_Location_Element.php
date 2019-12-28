<?php

class Template_Location_Element extends Template_Abstract_Element
{
	protected $key;
	protected $default;
	protected $Fuse; // Template_Root_Element
	
	public function __construct($key, $default = NULL)
	{
		$this->Container = $Container;
		$this->key = $key;
		$this->default = $default;
	}

	public function content(array $fill, array $options)
	{
		if($this->Fuse)
		{			
			$content = $this->Fuse->content($fill, $options);
		}
		elseif(isset($fill[$this->key]))
		{
			if(is_array($fill[$this->key])) throw new Exception("Locatie '$this->key' mag niet worden gevuld met een array");
			else $content = (string) $fill[$this->key];
		}
		elseif(isset($this->default))
		{
			$content = $this->default;
		}
		else
		{
			$content = "";
		}
		
		return $content;
	}
	
	/*
	 * Merk op dat het element dat hierin gefuseerd wordt NIET
	 * deze locatie ECHT vervangt. Het zorgt enkel voor een andere werking van content
	 */
	
	public function fuse(array $fuse)
	{
		if($this->Fuse)
		{
			$this->Fuse->fuse($fuse);
		}
		elseif(isset($fuse[$this->key]))
		{
			if(! $fuse[$this->key] instanceof Template_Processor) throw new Exception("Fuseren op '$this->key' kan enkel met een instancie van Template_Processor");
			$this->Fuse = $fuse[$this->key]->getRoot();
		}
	}
	
	public function tree($depth)
	{
		if($this->Fuse)
		{
			return $this->Fuse->tree($depth);
		}
		else
		{
			return $this->objectAsTree($depth) .  "[$this->key]";	
		}
	}
}