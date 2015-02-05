<?php

namespace ssa\runner\annotations;

use Doctrine\Common\Annotations\Annotation;

use ssa\ServiceMetadata;

/**
 * Description of RunnerHandler
 *
 * @Annotation
 * 
 * @author thomas
 */ 
class RunnerHandler {
    
    /**
     * set state magic method for cache method
     * @param type $array
     * @return \ssa\runner\converter\RunnerHandler
     */
    public static function __set_state($array) {
        $converter = new RunnerHandler();
        return $converter;
    }
	
	/**
	 * call before service call
	 *
	 * @param string $method the action name
	 * @param array $inputParameters service parameter, (service => the service call, service.method)
	 * @param ServiceMetadata $metaData
	 *
	 * @throw Exception if action must no be call
	 */
	public function before($method,array $inputParameters,ServiceMetadata $metaData) {
	
	}
	
	/**
	 * call before service call
	 *
	 * @param string $method the action name
	 * @param array $inputParameters service parameter, (service => the service call, service.method)
	 * @param mixed the service result before encoding
	 * @param ServiceMetadata $metaData
	 *
	 * can return value tranformed $result, encoder is call after this method
	 */
	public function after($method,array $inputParameters, $result, ServiceMetadata $metaData) {
		
	}
}
