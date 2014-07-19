<?php

namespace ssa\runner\resolver\impl;

use ssa\runner\resolver\ObjectResolverCOR;
use ssa\annotation\AnnotationUtil;
use ssa\runner\BadTypeException;

/**
 * Description of DefaultObjectResolver
 *
 * @author thomas
 */
class DefaultObjectResolver extends ObjectResolverCOR {
    
    /**
     * {@inheritdoc}
     */
    protected function canResolve(\ReflectionClass $class) {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function resolve(\ReflectionClass $class, $parameters, array &$commentType) {
        if (!is_array($parameters)) {
            throw new BadTypeException('', 'object', gettype($parameters));
        }
        
        $object = $this->instanciate($class, $parameters);
        foreach ($parameters as $paramName => $value) {
            $setter = $class->getMethod('set'.ucfirst($paramName));
            // test if the setter parameter is an object
            $parameters = $setter->getParameters();
            $classParameter = $parameters[0]->getClass();
           
            $methodParameters = AnnotationUtil::getMethodParameters($setter->getDocComment());
            $keys = \array_keys($methodParameters);
            if (count($methodParameters) === 1) {
                $firstParameter = $methodParameters[$keys[0]]; 
                if ($classParameter == null && class_exists($firstParameter[0])) {
                    $classParameter = new \ReflectionClass($firstParameter[0]);
                }
            }
            
            if ($classParameter != null) {
                $childObject = $this->parameterResolver->resolveObject($classParameter, $value, $methodParameters);
                $setter->invoke($object, $childObject);
            } else {
                if (isset($firstParameter)) {
                    $value = $this->parameterResolver->resolvePrimitive($value, $firstParameter);
                }
                $setter->invoke($object, $value);
            }
        }
        return $object;
    }

    /**
     * function class for instancate the class
     * @param \ReflectionClass $class the class name
     * @param array $parameters the parameters
     */
    protected function instanciate(\ReflectionClass $class, $parameters) {
        return $class->newInstance();
    }
}
