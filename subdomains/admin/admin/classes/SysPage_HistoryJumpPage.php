<?php
class SysPage_HistoryJumpPage extends SysPage_Abstract
{
	public function construct()
	{
		$offset = Request::$Post["id"];
		
		History::jumpTo($offset);
	}
}