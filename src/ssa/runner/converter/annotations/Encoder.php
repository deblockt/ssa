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
class Encoder {
    /**
     * the class use ton convert    
     * @var string 
     */
    public $value;
    
    /**
     * set state magic method for cache method
     * @param type $array
     * @return \ssa\runner\converter\annotations\Encoder
     */
    public static function __set_state($array) {
        $converter = new Encoder();
        $converter->value = $array['value'];
        return $converter;
    }
}
