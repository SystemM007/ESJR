<?php
class Ofc_Axis_YRight extends Ofc_Axis_Y
{
	public function setGridColour()
	{
		throw new Exception("You cannot control the grid color from the Ofc_Axis_YRight use the Ofc_Axis_Y object.");
	}
}