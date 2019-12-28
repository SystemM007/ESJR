<?php
class Ofc_Chart
{
	protected $data = array(
		"title" => null,
		"x_axis" => null,
		"y_axis" => null,
		"y_axis_right" => null,
		"x_legend" => null,
		"y_legend" => null,
		"elements" => array(),
		"bg_color" => null
	
	);
	
	public function __construct()
	{
		
	}
	
	public function setTitle(Ofc_Title $Object)
	{
		$this->add("title", $Object);
	}
	
	public function setXAxis(Ofc_XAxis $Object)
	{
		$this->add("x_axis", $Object);
	}
	
	public function setYAxis(Ofc_Title $Object)
	{
		$this->add("y_axis", $Object);
	}
	
	public function setYAxisRight(Ofc_YAxisRight $Object)
	{
		$this->add("y_axis_right", $Object);
	}

	public function setXLegend(Ofc_Legend_X $Object)
	{
		$this->add("x_legend", $Object);
	}
	
	public function setYLegend(Ofc_Legend_Y $Object)
	{
		$this->add("y_legend", $Object);
	}
	
	public function addElement(Ofc_Element $Object)
	{
		$this->data["elements"][] = $Object;
	}
	
	public function setBgColor(Ofc_BgColor $Object)
	{
		$this->add("bg_color", $Object);
	}
	
	private function addObject($key, Ofc_Object $Object)
	{
		if(!array_key_exists($key, $this->data)) throw new Exception("Tried to add Object to unknown key '$key'");
		$this->data[$key] = $Object->getData();
	}
	
	public function __toString()
	{
		return json_encode($data);
	}
}
?>