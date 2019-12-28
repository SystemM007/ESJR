<?php

class Fragment_Tag_Div extends Fragment_Tag_Basic
{

	public function __construct($id, array $attributes = array(), $content = "")
	{
		parent::__construct("div", $id, $attributes, $content);
	}
}
			