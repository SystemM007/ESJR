<?php
require("../environment/start.php");

Autoload::addPath(Dir::admin_classes);
Template::addPath(Dir::admin_templates);
new FunctionLoader(Dir::admin_functions);

Request::init();

/*
* voor de flash upload
* LET OP: dit moet VOOR User::init()
* en kan dus niet eenvoudig naar response.php worden verpaatst
*/
if(isset($_POST["Cookies"]))
{
	// 15-07-2014
	// $_COOKIE = json_decode(stripslashes($_POST["Cookies"]), true);
	$_COOKIE = json_decode($_POST["Cookies"], true);
	unset($_POST["Cookies"]);
}


User::init();

switch(Request::$Path[0])
{
	case NULL:
	case "":
		require(Dir::admin . "createAdmin.php");
	break;
	
	/*case "crossdomain.xml":
		header("Content-Type: text/xml; charset=utf-8");
		echo trim(new Template("crossdomain"));
	break;*/
	
	case "robots.txt" :
		header("Content-Type: text/plain; charset=utf-8");
		echo new Template("robotsDisallow");
	break;
		
	default:
		require(Dir::admin . "response.php");
}

Session::finish();