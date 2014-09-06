<?php

include 'serviceConfig.php';

use ssa\runner\ServiceRunner;
use ssa\Configuration;

try {
    list($service, $action) = explode('.', $_POST['service']);
    $serviceRunner = new ServiceRunner($service);
    echo $serviceRunner->runAction($action, array_merge($_POST, $_FILES));
} catch (Exception $ex) {
    header('Content-type: text/json');
    echo json_encode(array(
        'class' => get_class($ex),
        'errorCode' => $ex->getCode(),
        'errorMessage' => $ex->getMessage(),
        'errorFile' => $ex->getFile(),
        'errorLine' => $ex->getLine(),
        'errorTrace' => $ex->getTraceAsString(),
        'debug' => Configuration::getInstance()->getDebug()
    ));
}