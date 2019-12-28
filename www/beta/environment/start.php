<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", true);
date_default_timezone_set("Europe/Berlin");
ini_set('default_charset', 'utf-8');

// autoload initialiseren
require_once(dirname(__FILE__) . "/classes/Autoload.php");
Autoload::addPath(realpath(dirname(__FILE__)) . "/classes/");

ErrorHandler::setErrorHandler(E_ALL ^ E_STRICT ^ E_NOTICE);
ErrorHandler::setExceptionHandler();

// working directory initialiseren
chdir(dirname(__FILE__));
chdir("../");

// templates initialiseren
Template::addPath(Dir::environment_templates);

// MySql laden
// De error pagina's 403.php 404.php maken gebruik van de constante DONT_USE_MYSQL
// dat scheelt weer een onnodige verbinding leggen
if(! (defined("DONT_USE_MYSQL") && constant("DONT_USE_MYSQL"))) call_user_func_array(array("MySql","connect"), require(Dir::environment . "mysql.php"));

// Functies laden
new FunctionLoader(Dir::environment_functions);