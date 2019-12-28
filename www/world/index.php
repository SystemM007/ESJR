<?php
require("../environment/start.php");

Autoload::addPath(Dir::world_classes);
Template::addPath(Dir::world_templates);

set_exception_handler(array("Request", "exceptionHandler"));

Request::init();

