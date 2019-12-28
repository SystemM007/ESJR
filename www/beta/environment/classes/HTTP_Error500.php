<?php 
class HTTP_Error500 extends HTTP_Error
{
	protected function sendHeader()
	{
		header("HTTP/1.0 500 Internal Server Error");
	}
	
	protected function sendBody($message)
	{
		echo new Template("error", 
			array(
				"title" => "HTTP 500 - Internal Server Error",
				"errorimage" => Uri::errorpage . "internalservererror.jpg",
				"msg" => $message
			)
		);
	}
}?>