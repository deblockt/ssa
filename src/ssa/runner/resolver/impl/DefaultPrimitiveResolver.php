<?php

namespace ssa\runner\resolver\impl;

use ssa\runner\resolver\PrimitiveResolverCOR;
use ssa\runner\resolver\TypeNotSupportedException;

/**
 * Description of DefaultTypeResolver
 *
 * @author thomas
 */
class DefaultPrimitiveResolver extends PrimitiveResolverCOR {
    
    /**
     * {@inheritdoc}
     */
    protected function canResolve(array &$type) {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function resolve($value, array &$type) {
        try {
			if (isset($type[0])) {				
				\settype($value, trim($type[0], '\\'));
			}
        } catch (\Exception $e) {
            // le type demandé n'existe pas 
            throw new TypeNotSupportedException($type[0]);
        }
        return $value;
    }

}
