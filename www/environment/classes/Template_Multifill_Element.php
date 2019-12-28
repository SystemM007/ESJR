<?php

class Template_Multifill_Element extends Template_Namespace_Element
{	
	public function content(array $fill, array $options)
	{
		$Dataset = $fill[$this->key];
		
		if(!isset($Dataset)) return "";
		if(!($Dataset instanceof Matrix)) throw new Exception("De fill multifill '$this->key' is geen Matrix.");
		
		$content = "";
		$i = 0;
		foreach($Dataset as $fillArray)
		{
			$i++;
			$options["isFirstFill"] = ($i == 1);
			$options["isLastFill"] = ($i == $Dataset->count());
			$content .= $this->elementsToContent($fillArray, $options);
		}
		
		return $content;
	}
}