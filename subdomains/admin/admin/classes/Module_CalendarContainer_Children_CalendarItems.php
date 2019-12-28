<?php

class Module_CalendarContainer_Children_CalendarItems extends Module_Basic_Children
{
	protected $sortable = false;
	protected $displayCoreEnabled = true;
	
	protected function listWhere($where = "")
	{
		return "AND u_cores.module = 'CalendarItem' " . $where;
	}

	protected function makeCreateInput()
	{
		return (string) new Fragment_Button_Create("Nieuw agendapunt", $this->Core, "CalendarItem");
	}
	
	public function childTypeIsAllowed($module)
	{
		return ($module == "CalendarItem") ;
	}
	
	protected function headers(array $headers = array())
	{
		$headers = array_merge(array(
			"date" => "",
			"active" => "",
		),$headers);
		return parent::headers($headers);
	}
	
	protected function listSelect(array $select = array())
	{
		$select[] = "w_calendar.date";
		$select[] = "w_calendar.date >= CURDATE() AS active";
		return parent::listSelect($select);
	}
	
	protected function listJoin(array $join = array())
	{
		$join[] = "w_calendar";
		return parent::listJoin($join);
	}
	
	protected function listOrder()
	{
		return "w_calendar.date DESC";
	}
	
	
	protected function processData($data)
	{
		foreach($data as $rowId=>$row) $data->update($row["active"] ? "<span style='color:#91FF84;'>online</span>" : "<span style='font-weight:bold; color:#000; '>verouderd</span>", "active", $rowId);
		$data->date("date"); 
		return parent::processData($data);
	}
}