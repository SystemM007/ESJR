<?php

class Fragment_Tag_HistoryJumpLink extends Fragment_Tag_Basic
{

	protected $quoteStyle = "'";
	
	public function __construct($offset = 1)
	{
		if($offset < 0) throw new Exception("Offset mag niet negatief zijn", E_USER_ERROR);

		$history = History::get($offset);

		$onclick = new Fragment_JS_Page("HistoryJumpPage", $offset);
		
		$href = "Javascript:void(0);";
		
		$innerHTML = $history["name"];
		
		$this->tagName = "a";
		
		parent::__construct("a", NULL, compact("onclick", "href"), $innerHTML);
	}
}