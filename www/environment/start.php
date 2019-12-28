<?php
error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED));
ini_set("display_errors", true);
date_default_timezone_set("Europe/Berlin");
ini_set('default_charset', 'utf-8');

// autoload initialiseren
require_once(dirname(__FILE__) . "/classes/Autoload.php");
Autoload::addPath(realpath(dirname(__FILE__)) . "/classes/");

// FirePHP
$FirePhp = FirePHP::getInstance(true);
if($FirePhp->detectClientExtension())
{
	ob_start();
	// error_reporting(E_ALL | E_STRICT); <-- Een keer hacken in FB zodat alle notices en strict warings niet direct dood gaan
	$FirePhp->registerErrorHandler();
	$FirePhp->registerExceptionHandler();
}
else
{
	// Error handling
	set_error_handler(array("CustomException", "errorHandlerCallback"), E_ALL ^ E_STRICT ^ E_NOTICE);
}

// working directory initialiseren
chdir(dirname(__FILE__));
chdir("../");

// templates initialiseren
Template::addPath(Dir::environment_templates);

// MySql laden
call_user_func_array(array("MySql","connect"), require(Dir::environment . "mysql.php"));

// Functies laden
new FunctionLoader(Dir::environment_functions);
