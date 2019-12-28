<?php

// sessie MOET na USER, sommige objecten mogen anders niet worden geunserialised
Session::init();
$instanceId = Session::createInstance();

$Template = new Template("template");
$Template->fill("instanceId", $instanceId);

$Template->fill("tinyCompressor", new Fragment_Tag_ExternScript(Uri::admin_services . "tinymce-3.2.1.1-strip/tiny_mce_gzip.js"));

$scripts = "";

if (!isset($_SERVER['APP_ENV']) || $_SERVER['APP_ENV'] !== "dev") {
    $scripts .= new Fragment_Tag_ExternScript(Uri::admin_services . "firebug/firebugx.js");
}

if(!isset($_SERVER['APP_ENV']) || $_SERVER['APP_ENV'] !== "dev")
{
	$scripts .= new Fragment_Tag_ExternScript(Uri::admin_services . "bundle-prototype-1.6.1.js");
	$scripts .= new Fragment_Tag_ExternScript(Uri::admin_services . "bundle-scriptaculous-1.8.1.js");
	$scripts .= new Fragment_Tag_ExternScript(Uri::admin_services . "swfupload-2.2-lvdg/bundle-swfupload.js");
	$scripts .= new Fragment_Tag_ExternScript(Uri::admin_js . "bundle-admin.js");
	$scripts .= new Fragment_Tag_ExternScript(Uri::admin_js . "bundle-libs.js");
}
else
{
	$ScriptLoader = new ScriptLoader();
	$ScriptLoader->addDir(Dir::admin_services . "prototype-1.6.1.js/", Uri::admin_services . "prototype-1.6.1.js/");
	$ScriptLoader->addDir(Dir::admin_services . "scriptaculous-1.8.1.js/", Uri::admin_services . "scriptaculous-1.8.1.js/");
	$ScriptLoader->addDir(Dir::admin_services . "swfupload-2.2-lvdg/bundle-swfupload.js/", Uri::admin_services . "swfupload-2.2-lvdg/bundle-swfupload.js/");
	$ScriptLoader->addDir(Dir::admin_js . "libs.js/", Uri::admin_js . "libs.js/");
	$ScriptLoader->addDir(Dir::admin_js . "admin.js/", Uri::admin_js . "admin.js/");
	
	foreach($ScriptLoader->getUriList() as $uri) $scripts .= new Fragment_Tag_ExternScript($uri);
	$scripts .= '<script>Admin.debugEnabled = true;</script>';
	
}

$Template->fill("script", $scripts);	

if (isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] === "dev") {
    $response = (string)$Template;

    $response = str_replace(
        'http://admin.esjr.nl/',
        'http://localhost/admin/',
        $response
    );

    echo $response;
} else {
    echo $Template;
}
