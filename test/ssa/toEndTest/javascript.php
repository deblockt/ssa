<?php

include 'serviceConfig.php';

use ssa\converter\AngularJavascriptConverter;
use ssa\converter\RequireJsJavascriptConverter;
use ssa\converter\JavascriptConverter;
use ssa\converter\SimpleUrlFactory;

// get base url for generic javascript generator
// if you know your base url you can remove three next lines
$serverRequestURI = $_SERVER['REQUEST_URI'];
$startUrl = substr($serverRequestURI, 0, strrpos($serverRequestURI, '?'));
$url = substr($startUrl,0, strrpos($startUrl, '/'));

// create an url factory used for call webservice
$factory = new SimpleUrlFactory("http://$_SERVER[HTTP_HOST]$url/run.php?service={action}&test=true");
if (isset($_GET['type']) && $_GET['type'] === 'angular') {
    $converter = new AngularJavascriptConverter($_GET['service'], $factory);
} else if (isset($_GET['type']) && $_GET['type'] === 'requirejs') {	
	$converter = new RequireJsJavascriptConverter(trim($_GET['service'], '/'), $factory);
} else {
    $converter = new JavascriptConverter($_GET['service'], $factory);
}
echo $converter->convert();