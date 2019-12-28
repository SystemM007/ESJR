<?php

class Fragment_Tag_Basic extends Fragment_Abstract
{
	private $tagName;
	private $id;
	private $attributes = array();
	private $content = "";
	private $selfclosing;
	
	protected $quoteStyle = "\"";

	public function __construct($tagName, $id = NULL, array $attributes = array(), $content = "", $selfclosing = false)
	{
		if($content && $selfclosing) throw new Exception("Een selfclosing tag kan geen innerHTML (content) bevatten");
	
		$this->tagName = $tagName;
		$this->id = $id;
		$this->attributes($attributes);
		$this->content($content);
		$this->selfclosing = $selfclosing;
	}
	
	public function attributes(array $attributes)
	{
		if($attributes["id"] && $this->id)
		{
			trigger_error("Attribuut 'id' zowel in de attributen array als in contructor aangetroffen!",e);
		}

		$this->attributes = array_merge($this->attributes, $attributes);
	}
	
	public function content($content)
	{
		$this->content .= (string) $content;
	}
	
	public function create()
	{
		$attributes = "";
		
		foreach($this->attributes as $key => $val)
		{
			$attributes .= $key . "=" . $this->quoteStyle . $val . $this->quoteStyle;
		}
		
		$tag = "";
		
		$tag .= "<$this->tagName ";
		if($this->id) $tag .= "id=\"$this->id\"";
		$tag .= $attributes;
		if($this->selfclosing)
		{
			$tag .= " />";
		}
		else
		{
			$tag .= ">";
			$tag .= $this->content;
			$tag .= "</$this->tagName>";
		}
		return $tag;
	}
}
			