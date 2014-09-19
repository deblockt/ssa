<?php

include __DIR__.'/../../../vendor/autoload.php';

use ssa\ServiceManager;
use ssa\Configuration;

Configuration::getInstance()->configure(array(
    'debug' => true,
   // 'cacheMode' => 'file',
   // 'cacheDirectory' => __DIR__.'/cache'
));
ServiceManager::getInstance()->registerAllServices(array(
    'helloWorldService' => array('class' => 'ssa\toEndTest\HelloWorld')
));
