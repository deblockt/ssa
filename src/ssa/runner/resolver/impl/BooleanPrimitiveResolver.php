<?php

namespace ssa\runner\resolver\impl;

use ssa\runner\resolver\PrimitiveResolverCOR;
use ssa\runner\resolver\TypeNotSupportedException;

/**
 * Description of DefaultTypeResolver
 *
 * @author thomas
 */
class BooleanPrimitiveResolver extends PrimitiveResolverCOR {
    
    /**
     * {@inheritdoc}
     */
    protected function canResolve(array &$type) {
        return isset($type[0]) && ($type[0] == 'bool' || $type[0] == 'boolean');
    }

    /**
     * {@inheritdoc}
     */
    protected function resolve($value, array &$type) {
        return $value == 'true';
    }

}
