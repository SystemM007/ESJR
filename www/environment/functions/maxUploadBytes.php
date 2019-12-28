<?php
function maxUploadBytes(){
	$val = trim(ini_get('upload_max_filesize'));
	$last = strtolower($val{strlen($val)-1});
	switch($last) {
		case 'g':$val *= 1024;
		case 'm':$val *= 1024;
		case 'k':$val *= 1024;
	}
	return $val;
}