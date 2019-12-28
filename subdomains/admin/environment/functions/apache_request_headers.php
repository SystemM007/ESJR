<?php
function apache_request_headers() {
	foreach($_SERVER as $key=>$value) {
		if (substr($key,0,5)=="HTTP_") {
			$key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5)))));
			$out[$key]=$value;
		}
	}
	return $out;
}
