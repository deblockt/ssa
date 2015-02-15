<?php

namespace ssa\converter\annotations;

use Doctrine\Common\Annotations\Annotation;


/**
 * this annotation allow to add personal javascript on generated service
 *
 * @Annotation
 * 
 * @author thomas
 */ 
class  AddJavascript {
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
        $converter = new AddJavascript();
        $converter->value = $array['value'];
        return $converter;
    }
}
