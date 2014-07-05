<?php

namespace ssa\runner\resolver;

/**
 * ResolveObject
 *
 * @author thomas
 */
interface ObjectResolver {
        
    /**
     * resolve an object
     * contruct the object and return it
     * 
     * @param ReflectionClass $class Class of the object
     * @param array $parameters parameters list of the object 
     *                          array(
     *                              'prop1' => 'value1', 
     *                              'prop2' => array(
     *                                              'prop1' => 'value2',
     *                                              'prop2' => null
     *                                          )
     *                          )
     *                          in this exemple the prop2 attribut is an other object
     * @param mixed $commentType the type write in the comment parameter
     * @throws \ssa\runner\BadTypeException if a type error occurs     
     * 
     * @return object the consutructed object
     */
    public function resolveObject(\ReflectionClass $class, $parameters,array &$commentType);
    
}
