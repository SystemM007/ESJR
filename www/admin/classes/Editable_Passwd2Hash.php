<?php

class Editable_Passwd2Hash extends Editable_Passwd
{
	protected function rewriteInputPost($value)
	{
		return md5($value);
	}
}