<?php

namespace ssa;

/**
 * Description of ServiceMetadata
 *
 * @author thomas
 */
class ServiceMetadata {
    /**
     *
     * @var \ReflectionClass 
     */
    private $class;

    /**
     * all method to convert
     * @var array 
     */
    private $supportedMethod;
    
    /**
     * the name of the service
     * @var string
     */
    private $serviceName;
    
    /**
     * 
     * @param string $serviceName the symbolic name for the service
     * @param string|\ReflectionClass $className the class to convert
     * @param array $supportedMethod all method to convert
     */
    public function __construct($serviceName, $className, array $supportedMethod = array()) {
        if (gettype($className) == 'string') {
            $this->class = new \ReflectionClass($className);
        } else {
            $this->class = $className;
        }
        $this->supportedMethod = $supportedMethod;
        $this->serviceName = $serviceName;
    }
    
    /**
     * get the class
     * @return \ReflectionClass
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * get list of supported method
     * @return array
     */
    public function getMethods() {
        return $this->supportedMethod;
    }
    
    /**
     * the symbolique service name
     * @return string
     */
    public function getServiceName() {
        return $this->serviceName;
    }


}
