<?php
class Fragment_Button_Url extends Fragment_Button_Basic
{
	public function __construct($value, $url)
	{
		parent::__construct($value, "location.href=\"$url\"");
	}
}
?>