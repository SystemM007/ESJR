<?php

class MainPageButton extends Fragment_Button_HistoryJump
{
	public function __construct()
	{
		$offset = History::count() - 1;
		parent::__construct($offset);
	}
}