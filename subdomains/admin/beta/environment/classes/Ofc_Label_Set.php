<?php

class Ofc_Label_Set extends Ofc_Label_Abstract
{
	public function __construct()
	{
		$this->data = array_merge($this->data, array(
			"steps" => 0,
			"labels" => array(),
		));
	}
	
	public function setSteps( $steps )
	{
		$this->data["steps"] = (int) $steps;
	}
	
	/*
	 * setLabels is the destructive version of addLabels
	 */
	public function setLabels(array $labels )
	{
		$this->data["labels"] = array();
		$this->addLabels($labels);
	}
	
	public function addLabels(array $labels)
	{
		foreach($labels as $label)
		{
			if($label instanceof Ofc_Label_Element)
			{
				$label = $label->getData();
			}
			else
			{
				$label = (string) $label;
			}
			$this->data["labels"][] = $label;
		}
	}
}