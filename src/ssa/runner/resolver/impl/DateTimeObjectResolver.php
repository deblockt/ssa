<?php

namespace ssa\runner\resolver\impl;

use ssa\runner\resolver\ObjectResolverCOR;
use ssa\runner\resolver\TypeNotSupportedException;


/**
 * Description of DateTimeObjectResolver
 * conevert a parameter into a datetime
 * the parameter of the type is the format
 * 
 * \DateTime(d/m/Y)
 * 
 * @author thomas
 */
class DateTimeObjectResolver extends ObjectResolverCOR {
    
    /**
     * {@inheritdoc}
     */
    protected function canResolve(\ReflectionClass $class) {
        return $class->getName() == 'DateTime';
    }

    /**
     * {@inheritdoc}
     */
    protected function resolve(\ReflectionClass $class, $parameters, array &$commentType) {
        $format = isset($commentType[1]) ? $commentType[1] : 'm/d/Y H:i:s';
        
        $return =  \DateTime::createFromFormat($format, $parameters);
        if ($return === false) {
            // TODO faire une exception avec un message d'erreur correcte
            throw new TypeNotSupportedException($commentType[0], $format);
        }
        return $return;
    }

}
