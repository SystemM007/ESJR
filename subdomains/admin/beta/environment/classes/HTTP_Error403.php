<?php 
class HTTP_Error403 extends HTTP_Error
{
	protected function sendHeader()
	{
		header("HTTP/1.0 403 Forbidden");
	}
	
	protected function sendBody($message)
	{
		echo new Template("error", 
			array(
				"title" => "HTTP 403 - Forbidden",
				"errorimage" => Uri::errorpage . "forbidden.jpg",
				"msg" => $message
			)
		);
	}
}?>