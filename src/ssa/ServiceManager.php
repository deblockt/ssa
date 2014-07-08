<?php

namespace ssa;

use ssa\ServiceMetadata;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * This class contains the list of accessible service
 * each service have a symbolique name
 * 
 * @author thomas
 */
class ServiceManager {
    /**
     * array associate all services name with the real classname
     * @var type 
     */
    private $services;
    
    /**
     * 
     * @var type 
     */
    private static $instance;
    
    /**
     * return a service manager
     * @return \ssa\ServiceManager
     */
    public static function getInstance() {
        if (self::$instance == null) {
            AnnotationRegistry::registerAutoloadNamespace(
                'ssa\runner\converter\annotations', 
                __DIR__.'/../'
            );
            self::$instance = new ServiceManager();
        }
        return self::$instance; 
    }
    
    private function __construct() {
        
    }
    
    /**
     * register a service
     * @param sting $symbolicName the symbolique name
     * @param string $class the class name
     * @param array $supportedMethod list of supported method
     */
    public function registerService($symbolicName, $class, array $supportedMethod = array()) {
        $this->services[$symbolicName] = new ServiceMetadata($symbolicName, $class, $supportedMethod);
    }
        
    /**
     * register all services 
     * @param array $services array(
     *  'serviceName' => array(
     *      'class' => 'namespace\className',
     *      'methods' => array('method1','method2','method3')
     *  ),
     *  ...
     * )
     * if no methods are specified, all method are translated
     */
    public function registerAllServices(array $services) {
        foreach ($services as $serviceName => $service) {
            $this->registerService(
                $serviceName, 
                $service['class'],
                isset($service['methods']) ? $service['methods'] : array() 
            );
        }
    }
    
    /**
     * return the class name for a symbolique name
     * @param string $symbolicName
     * @return ServiceMetadata the class name
     */
    public function getService($symbolicName) {
        if (!isset($this->services[$symbolicName])) {
            throw new \ssa\ServiceNotRegistredException($symbolicName);
        }
        return $this->services[$symbolicName];
    }
}
