<?php
function request_headers() {
	
	return array_change_key_case(apache_request_headers());


}