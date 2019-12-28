<?php
class Ofc_Colour
{
	/*
	 * This class can be heavily extended with options how to formulate the color
	 *  array(r,g,b) (hex or decimal), or with the option for giving the color with 3 numbers #rgb
	 * 
	 * The only valid $colour syntax now is #rrggbb
	 * 
	 * The output of this class is always in the #rrggbb style, 
	 * so for now the function of this class is to act as a validator
	 */
	
	protected $string;
	
	public function __construct($colour)
	{
		if($colour instanceof Ofc_Colour)
		{
			$this->string = (string) $colour;
		}
		else
		{
			if(!preg_match("/#?(\d{6})/", $colour, $matches))
			{
				throw new Exception("Colour syntax error: '$colour'");
			}
			else
			{
				$this->string = "#" . $matches[1];
			}
		}
	}
	
	public function __toString()
	{
		return $this->string;
	}
}