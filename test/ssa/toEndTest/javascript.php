<?php

include 'serviceConfig.php';

use ssa\converter\JavascriptConverter;
use ssa\converter\SimpleUrlFactory;

$url = substr($_SERVER['REQUEST_URI'],0, strrpos($_SERVER['REQUEST_URI'], '/'));
$factory = new SimpleUrlFactory("http://$_SERVER[HTTP_HOST]$url/run.php?service={action}&test=true");
$converter = new JavascriptConverter($_GET['service'], $factory);

echo $converter->convert();