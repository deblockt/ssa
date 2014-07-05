<?php

namespace ssa\converter;

use ssa\ServiceMetadata;
use ssa\annotation\AnnotationUtil;
use ssa\ServiceManager;
use ssa\Configuration;

/**
 * Class for convert service PHP to Other thing
 * It's an abstract class, extends for add an support language
 */
abstract class ServiceConverter {

    /**
     * Service metadata
     * @var ServiceMetadata 
     */
    private $metaData;
        
    /**
     * the factory for the url
     * @var UrlFactory 
     */
    private $urlFactory;
    
    /**
     * 
     * @param string $metaData the service metadata
     */
    public function __construct($serviceName, UrlFactory $urlFactory) {
        $this->metaData = ServiceManager::getInstance()->getService($serviceName);
        $this->urlFactory = $urlFactory;
    }
    
    /**
     * function to convert the class
     * 
     * @return string the converted class
     */
    public function convert() {        
        $cacheProvider = Configuration::getInstance()->getCacheProvider();
        if ($cacheProvider != NULL) {
            $cacheName = 'javacript['.$this->metaData->getServiceName().']';
            $updateFileClass = filemtime($this->metaData->getClass()->getFileName());

            $cacheResult = $cacheProvider->fetch($cacheName);
            if ($cacheResult != NULL && $cacheResult['updateTime'] >= $updateFileClass) {
                return $cacheResult['converted'];
            }
        }
        
        $return = $this->convertClass($this->getServiceName());
        if (count($this->metaData->getSupportMethod()) > 0) {
            foreach ($this->metaData->getSupportMethod() as $methodName) {
                $method = $this->metaData->getClass()->getMethod($methodName);
                $return .= $this->createMethod($method);
            }
        } else {
            foreach ($this->metaData->getClass()->getMethods() as $method) {
                $return .= $this->createMethod($method);         
            }
        }
        
        if ($cacheProvider != NULL) {
            $cacheData = array(
                'updateTime' => $updateFileClass,
                'converted' => $return
            );
            $cacheProvider->save($cacheName, $cacheData);
        }
        
        return $return;
    }
    
    /**
     * convert a method into new language
     * @param ReflectionMethod $method
     */
    private function createMethod(\ReflectionMethod $method) {
        $comment = $method->getDocComment();
        $params = AnnotationUtil::getMethodParameters($comment);
        return $this->convertMethod($method->getName(), $params, $comment);
    }
    
    /**
     * 
     * @param string $methodName the method name
     * @param array $params  the method parameter array('paramName' => 'paramType', ...)
     * @param string $comment the method comment
     */
    protected abstract function convertMethod($methodName, $params, $comment);
    
    /**
     * call when class ant be converted
     * 
     * @param string the className
     */
    protected abstract function convertClass($className);
      
    /**
     * @return string return the service key {@see ServiceMap}
     */
    protected function getServiceName() {
        return $this->metaData->getServiceName();
    }
    
    /**
     * construct an URL for execute the action
     * @param type $action
     */
    protected function constructUrl($action) {
        return $this->urlFactory->constructUrl($action);
    }
}
