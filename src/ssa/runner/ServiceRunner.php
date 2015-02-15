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

use ssa\runner\converter\annotations\Encoder;
use ssa\runner\annotations\RunnerHandler;

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
     * @param type $smethod
     * @param type $inputParameters
     * @return type
     * 
     * @throws ActionNotSupportedException
     * @throws \ssa\runner\resolver\TypeNotSupportedException
     * @throws MissingParameterException
     * @throws ClassNotFoundException
     */
    private function runActionWithoutTryCatch($smethod, $inputParameters = array()) {
        if (count($this->metaData->getMethods()) > 0 
            && 
            !in_array($smethod, $this->metaData->getMethods())
        ) {            
            throw new ActionNotSupportedException($smethod);
        }
        
        $method = $this->metaData->getClass()->getMethod($smethod);
        // lecture de l'annotation de la méthode

        $annotationReader = $this->getAnnotationReader();        
			
				
		// read anotations and check if a runner handler is present
		$methodAnnos = $annotationReader->getMethodAnnotations($method);
		
        foreach ($methodAnnos as $anno) {
			// call before handler
			if ($anno instanceof RunnerHandler && method_exists($anno, 'before')) {
				$anno->before($smethod, $inputParameters, $this->metaData);
			}
		}
		
		
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
		
		$encoder = null;        
		foreach ($methodAnnos as $anno) {
			if ($anno instanceof Encoder) {
				if (!class_exists($anno->value)) {
					throw new ClassNotFoundException($anno->value);
				}
				$encoder = new $anno->value();
				break;
			}
			
			// call after handler
			if ($anno instanceof RunnerHandler && method_exists($anno, 'after')) {
				$after = $anno->after($smethod, $inputParameters, $result, $this->metaData);
				if ($after != null) {
					$result = $after;
				}
			}
		}

		if ($encoder == null) {
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
            header('Content-type: text/json');
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
