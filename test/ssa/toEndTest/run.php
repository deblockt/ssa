<?php

include 'serviceConfig.php';

use ssa\runner\ServiceRunner;

list($service, $action) = explode('.', $_POST['service']);

$serviceRunner = new ServiceRunner($service);
echo $serviceRunner->runAction($action, $_POST);