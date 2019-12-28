<?php 
class HTTP_Error404 extends HTTP_Error
{
	protected function sendHeader()
	{
		header("HTTP/1.0 404 Not Found");
	}
	
	protected function sendBody($message)
	{
		echo new Template("error", 
			array(
				"title" => "HTTP 404 - Not Found",
				"errorimage" => Uri::errorpage . "notfound.jpg",
				"msg" => $message
			)
		);
	}
}?>