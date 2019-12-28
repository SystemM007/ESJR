<?php
class ClockLog extends Clock
{
	public static $version;

	public function finish($action = "")
	{
		$duration = parent::finish();
		
		MySql::insert(array(
			"table" => "x_clock",
			"values" => array("action" => "$action", "duration" =>$duration, "version" => self::$version)
		));
	}
}
?>