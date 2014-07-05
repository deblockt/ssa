<?php

namespace ssa\runner\resolver\impl;

use ssa\runner\resolver\PrimitiveResolverCOR;

/**
 * Description of ArrayPrimitiveResolver
 * resolve typed array type simple array are typed by {@see DefaultPrimitiveResolver}
 * 
 * array(primitive)
 * array(ClassPath)
 * 
 * @author thomas
 */
class ArrayPrimitiveResolver extends PrimitiveResolverCOR {
    
    /**
     * {@inheritdoc}
     */
    protected function canResolve(array &$type) {
        return $type[0] == 'array' && count($type) > 1  ;
    }

    /**
     * {@inheritdoc}
     */
    protected function resolve($value,array &$type) {        
        
        $arrayType = $type[1];
        $isClass = \class_exists($arrayType);
        if ($isClass) {
            $reflexionClass = new \ReflectionClass($arrayType);
        } else {
            setType($arrayType, 'array');       
        }
        
        settype($value, 'array');
        $subparameter = array($type[1]);
        foreach ($value as &$subValue) {
            if ($isClass) {
                $subValue = $this->parameterResolver->resolveObject($reflexionClass, $subValue, $subparameter);
            } else {
                $subValue = $this->parameterResolver->resolvePrimitive($subValue, $arrayType, $subparameter);
            }
        }
        
        return $value;
    }

}
