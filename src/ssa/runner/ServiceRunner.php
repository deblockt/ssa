<?php

namespace ssa\runner;

use ssa\ServiceMetadata;
use ssa\annotation\AnnotationUtil;
use ssa\runner\resolver\ParameterResolver;
use ssa\runner\resolver\TypeNotSupportedException;
use ssa\runner\resolver\impl\DefaultParameterResolver;
use ssa\runner\converter\DefaultJsonEncoder;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use ssa\ServiceManager;
use ssa\Configuration;
use Doctrine\Common\Annotations\CachedReader;
use ssa\runner\BadTypeException;

/**
 * this class can call a service
 */
class ServiceRunner {
    /**
     *
     * @var ServiceMetadata
     */
    private $metaData;
    
    /**
     *
     * @var \ssa\runner\resolver\ParameterResolver
     */
    private $parameterResolver;
    
    /**
     * 
     * @param string|ServiceMetadata $service service metadata
     * @param ParameterResolver $parameterResolver the parameter resolver, 
     *                          if null use the default parameter resolver
     */
    public function __construct($service, ParameterResolver $parameterResolver = null) {
        if (gettype($service) == 'string') {
            $this->metaData = ServiceManager::getInstance()->getService($service);
        } else {
            $this->metaData = $service;
        }
        if ($parameterResolver == null) {
            $this->setParameterResolver(DefaultParameterResolver::createDefaultParameterResolver());
        } else {
            $this->setParameterResolver($parameterResolver);
        }
    }
    
    /**
     * set the parameter resolver
     * @param \ssa\runner\ParameterResolver $parameterResolver the new resolver
     */
    public function setParameterResolver(ParameterResolver $parameterResolver) {
         $this->parameterResolver = $parameterResolver;    
    }
    
    /**
     * run the action without try catch for framework exception
     * @param type $method
     * @param type $inputParameters
     * @return type
     * 
     * @throws ActionNotSupportedException
     * @throws \ssa\runner\resolver\TypeNotSupportedException
     * @throws MissingParameterException
     * @throws ClassNotFoundException
     */
    private function runActionWithoutTryCatch($method, $inputParameters = array()) {
        if (count($this->metaData->getMethods()) > 0 
            && 
            !in_array($method, $this->metaData->getMethods())
        ) {            
            throw new ActionNotSupportedException($method);
        }
        
        $method = $this->metaData->getClass()->getMethod($method);
        // lecture de l'annotation de la méthode
        
        AnnotationRegistry::registerAutoloadNamespace(
            '\ssa\runner\converter\annotations', 
            __DIR__."/converter/annotations/"
        );
        
        $docParameters = AnnotationUtil::getMethodParameters($method->getDocComment());
        $parameters = $method->getParameters();
        $parametersValue = array();
        // for each parameters create a value
        /* @var $parameter  \ReflectionParameter */
        foreach ($parameters as $parameter) {
            $currentValue = null;
            // if it's an object create if
            $class = $parameter->getClass();
            $name = $parameter->getName();
            try {
                if (!$parameter->isDefaultValueAvailable() && !isset($inputParameters[$name])) {
                    throw new MissingParameterException($name);
                } else if (isset($inputParameters[$name])){                    
                    if (!isset($docParameters[$name]) || $docParameters[$name] == NULL) {
                        $docParameters[$name] = array();
                    }
                    if ($class != null) {
                        $currentValue = $this->parameterResolver->resolveObject($class, $inputParameters[$name], $docParameters[$name]);
                    } else if (isset($docParameters[$name])) {
                        // if it's an primitive we check it
                        $currentValue = $this->parameterResolver->resolvePrimitive($inputParameters[$name], $docParameters[$name]);
                    }
                } else {
                    $currentValue = $parameter->getDefaultValue();
                }
            } catch (TypeNotSupportedException $ex) {
                $ex->setVarname($name);
                throw $ex;
            } catch (BadTypeException $ex) {
                $ex->setParamName($name);
                throw $ex;
            }
            $parametersValue[] = $currentValue;
        }
        $service = $this->metaData->getInstance();
        $result = $method->invokeArgs($service, $parametersValue);
        
        $annotationReader = $this->getAnnotationReader();        
        $methodAnnotations = $annotationReader->getMethodAnnotations($method, 'ssa\runner\converter\annotations\Encoder');
        $encoder = null;
        if (count($methodAnnotations) > 0) {
            if (!class_exists($methodAnnotations[0]->value)) {
                throw new ClassNotFoundException($methodAnnotations[0]->value);
            }
            $encoder = new $methodAnnotations[0]->value();
        } else {
            $encoder = new DefaultJsonEncoder();
        }
        
        $encodedResult = $encoder->encode($result);
        $headers = $encoder->getHeaders();
        if (!headers_sent()) {
            foreach ($headers as $key => $value) {
                header($key.': '.$value);
            }
        }
        return $encodedResult;
    }
    
    /**
     * run an action of the class
     * @param $method  the method name to run
     * @param $inputParameters all parameters
     *
     */
    public function runAction($method, $inputParameters = array()) {
        try {
            return $this->runActionWithoutTryCatch($method, $inputParameters);
        } catch (\Exception $ex) {
            return json_encode(array(
                'class' => get_class($ex),
                'errorCode' => $ex->getCode(),
                'errorMessage' => $ex->getMessage(),
                'errorFile' => $ex->getFile(),
                'errorLine' => $ex->getLine(),
                'errorTrace' => $ex->getTraceAsString(),
                'debug' => Configuration::getInstance()->getDebug()
            ));
        }
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
