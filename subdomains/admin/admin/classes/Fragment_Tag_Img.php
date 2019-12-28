<?php

class Fragment_Tag_Img extends Fragment_Tag_Basic
{

	public function __construct($src, $alt="", array $attributes = array())
	{
		$attributes = array_merge($attributes, compact("src"));
		if($alt) $attributes = array_merge($attributes, compact("alt"));
		
		parent::__construct("img", NULL, $attributes);
	}
}
			