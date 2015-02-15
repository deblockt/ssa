<?php

namespace ssa\converter;

use ssa\ServiceMetadata;
use ssa\annotation\AnnotationUtil;
use ssa\ServiceManager;
use ssa\Configuration;

use ssa\converter\annotations\AddJavascript;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;

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
     * @param string|ssa\ServiceMetadata $service the service metadata
     * @param UrlFactory $urlFactory the url factory
     * 
     */
    public function __construct($service, UrlFactory $urlFactory) {
        if (gettype($service) == 'string') {
            $this->metaData = ServiceManager::getInstance()->getService($service);
        } else {
            $this->metaData = $service;
        }
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
		
		// add perso javascript content
		$return .= $this->addPersoJavascript($this->metaData->getClass(), $this->getServiceName());
		
        if (count($this->metaData->getMethods()) > 0) {
            foreach ($this->metaData->getMethods() as $methodName) {
                $method = $this->metaData->getClass()->getMethod($methodName);
                $return .= $this->createMethod($method);
            }
        } else {
            foreach ($this->metaData->getClass()->getMethods() as $method) {
                $return .= $this->createMethod($method);         
            }
        }
        
        $return .= $this->endConvertClass($this->getServiceName());
        
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
	 * add perso Javascript file on generated service
	 * if annotation AddJavascript is on the class definition
	 * file specified on parameter is add on the generated file
	 *
	 * the file generated can define a module function, if you need add function on the module this function must be used
	 * this function have one parameter the current generated module
	 */
	protected function addPersoJavascript($class, $serviceName) {
		$annotationReader = $this->getAnnotationReader();        
			
		// read anotations and check if a runner handler is present
		$classAnno = $annotationReader->getClassAnnotation($class, 'ssa\converter\annotations\AddJavascript');
		
		if ($classAnno != null) {
			$completePath = $class->getFileName();
			$dirPath = substr($completePath, 0, strrpos($completePath, DIRECTORY_SEPARATOR) + 1);
			
			return 
				file_get_contents($dirPath.$classAnno->value) . "\n"
				. 'if (module) {module('.$serviceName.')}'."\n";
		}
		
		return '';
	}
	
    /***
     * function call when the converter have convert all methods
     * 
     * @param $serviceName the name of the service to convert
     * 
     * @return string the string to add at the end of the service converted
     */
    protected function endConvertClass($serviceName) {
        // Default do nothing
    }
    
    /**
     * convert a method into new language
     * @param ReflectionMethod $method
     */
    private function createMethod(\ReflectionMethod $method) {
        $comment = $method->getDocComment();
        $params = AnnotationUtil::getMethodParameters($comment);
        // get params with reflexion methods
        $parameters = $method->getParameters();
        $realParameters = array();
        foreach ($parameters as $parameter) {
            // @var \ReflectionParameter $parameter
            $paramName = $parameter->getName();
            if (isset($params[$paramName])) {
                $realParameters[$paramName] = $params[$paramName];
            } else if ($parameter->getClass() != null) {
                $realParameters[$paramName] = array($parameter->getClass()->getName());
            } else {
                $realParameters[$paramName] = array('undefined');
            }
        }
        return $this->convertMethod($method->getName(), $realParameters, $comment);
    }
    
    /**
     * 
     * @param string $methodName the method name
     * @param array $params  the method parameter array('paramName' => 'paramType', ...)
     * @param string $comment the method comment
     */
    protected abstract function convertMethod($methodName, $params, $comment);
    
    /**
     * call when class must be converted
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
	
	
	private function getAnnotationReader() {
        $configuration = Configuration::getInstance();
        $cacheMode = $configuration->getCacheMode();
        $defaultAnnotationReader = new AnnotationReader();
        if ($cacheMode == 'none') {
            return $defaultAnnotationReader;            
        } else {
            return new CachedReader(
                $defaultAnnotationReader,
                $configuration->getCacheProvider(),
                $configuration->getDebug()
            );            
        }
    }
	
}
