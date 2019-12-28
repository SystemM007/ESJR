<?php

function xjson_decode($data){

	static $J;
	
	if(!$J){	
		$J = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
	}
	
	return $J->decode($data);
	
}