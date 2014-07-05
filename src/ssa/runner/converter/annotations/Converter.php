<?php

namespace ssa\runner\converter\annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * Description of Converter
 *
 * @Annotation
 * 
 * @author thomas
 */
class Converter {
    /**
     * the class use ton convert    
     * @var string 
     */
    public $value;
    
    /**
     * set state magic method for cache method
     * @param type $array
     * @return \ssa\runner\converter\annotations\Converter
     */
    public static function __set_state($array) {
        $converter = new Converter();
        $converter->value = $array['value'];
        return $converter;
    }
}
