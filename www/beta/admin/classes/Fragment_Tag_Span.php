<?php

class Fragment_Tag_Span extends Fragment_Tag_Basic
{

	public function __construct($id, $attributes = array(), $content = "")
	{
		parent::__construct("span", $id, $attributes, $content);
	}
}
			