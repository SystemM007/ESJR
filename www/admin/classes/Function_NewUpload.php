<?php

class Function_NewUpload extends Function_Abstract
{
	public function startFunction()
	{
		$q = stripslashes($_GET["query"]);
		$_POST = json_decode($q, true);
		
		Response::isUpload();
		Response::sendNoUploadResponse();
	}
}
	