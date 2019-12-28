<?php

class Fragment_PageNumbers extends Fragment_Abstract
{
	public function __construct($num, $curNum = -1, $dataField = "page", $data = array(), $page = "", $options = array(), $confirm = ""){

		$this->num = (int) $num;
		$this->curNum = (int) $curNum;
		$this->dataField = (string) $dataField;
		$this->data = $data;
		$this->page = $page;
		$this->options = $options;
		$this->confirm = $confirm;
	
	}
	
	public function create(){
	
		$html = "";
		
		for($i = 1; $i <= $this->num; $i++){
		
			$data = array_merge($this->data, array($this->dataField => $i));
			
			$html .= "<span class='pageNumber' ";
			if($i == $this->curNum){
				$html .= "id='pageNumberCurrent'";
			}
			else{
				$html .= "onClick='" . new Action($data, $this->page, $this->options, $this->confirm) . "'";
			}
			$html .= ">";
			$html .= $i;
			$html .= "</span>";
		}
		
		return $html;
		
	}
	
	public function pushPageNumbers(AdminPage $page){
		
		$page->pagenumbers($this);
	
	}

}