<?php

function json_encode($data){

	static $J;
	
	if(!$J){	
		$J = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
	}
	
	return $J->encode($data);
	
}