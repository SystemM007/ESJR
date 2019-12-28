<?php
class Ofc_PrettyJson
{
	protected $string;
	
	public function __construct($data)
	{
		if(!is_string($data)) throw new Exception("\$data is not a string");
		if(! ($dataDecoded = json_decode($data)) ) throw new Exception("\$data is not valid json");
		
		// ensure we have the normal, whitespaceless encoding
		$data = json_encode($dataDecoded);
		
		$this->string = $this->json_format($data);
	}
	
	public function __toString()
	{
		return $this->string;
	}
	
	protected function json_format($json)
	{
	    $tab = "  ";
	    $new_json = "";
	    $indent_level = 0;
	    $in_string = false;
	
	/*
	 commented out by monk.e.boy 22nd May '08
	 because my web server is PHP4, and
	 json_* are PHP5 functions...
	
	    $json_obj = json_decode($json);
	
	    if($json_obj === false)
	        return false;
	
	    $json = json_encode($json_obj);
	*/
	    $len = strlen($json);
	
	    for($c = 0; $c < $len; $c++)
	    {
	        $char = $json[$c];
	        switch($char)
	        {
	            case '{':
	            case '[':
	                if(!$in_string)
	                {
	                    $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
	                    $indent_level++;
	                }
	                else
	                {
	                    $new_json .= $char;
	                }
	                break;
	            case '}':
	            case ']':
	                if(!$in_string)
	                {
	                    $indent_level--;
	                    $new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
	                }
	                else
	                {
	                    $new_json .= $char;
	                }
	                break;
	            case ',':
	                if(!$in_string)
	                {
	                    $new_json .= ",\n" . str_repeat($tab, $indent_level);
	                }
	                else
	                {
	                    $new_json .= $char;
	                }
	                break;
	            case ':':
	                if(!$in_string)
	                {
	                    $new_json .= ": ";
	                }
	                else
	                {
	                    $new_json .= $char;
	                }
	                break;
	            case '"':
	                if($c > 0 && $json[$c-1] != '\\')
	                {
	                    $in_string = !$in_string;
	                }
	            default:
	                $new_json .= $char;
	                break;                   
	        }
	    }
	
	    return $new_json;
	}
}