<?php

class Fragment_HTMLColumns extends Fragment_Abstract
{
	protected $columns;

	public function __construct()
	{
		$columns = func_get_args();
		
		if(!$columns) throw new Exception("Er moet minimaal één kolom gegeven worden");

		$this->columns = $columns;
	}
	
	public function create()
	{
		$width = 100 / count($this->columns);
		$html = "";
		
		foreach($this->columns as $id)
		{
			$html .= new Fragment_Tag_Div("", array("style" => "float:left; width:$width%; "),
				new Fragment_Tag_Div($id, array("style" => "margin-left:10px; margin-right:10px"))
			);
		}
		$html .= new Fragment_Tag_Div("", array("style" => "clear:both; height:1px; "));
		
		
		return $html;
	}
}