<?php

namespace ssa\runner\resolver;

/**
 * Service for convert parameter (GET or SET) into method parameter (primitive or object, etc...)
 *
 * @author thomas
 */
interface PrimitiveResolver {

    /**
     * convert the primitive type into a typed value
     * 
     * @param string $value the primitive value
     * @param array $type the excpected type, exctract with the method comment
     * 
     * @return the typed value
     */
    public function resolvePrimitive($value, array &$type);
}
