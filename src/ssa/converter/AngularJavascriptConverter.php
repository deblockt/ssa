<?php

namespace ssa\converter;

use ssa\converter\JavascriptConverter;

/**
 * Description of AngularJavascriptConverter
 *
 * @author thomas
 */
class AngularJavascriptConverter extends JavascriptConverter {
    
    /**
     * create angular service
     * 
     * @param type $className
     * @return type
     */
    protected function convertClass($className) {
        $txt = 'var ssaModule = angular.module(\'ssa\');'.$this->END_OF_LINE;
        $txt .= 'ssaModule.factory(\''.$className.'\',[\'ssa\', function(ssa){'.$this->END_OF_LINE;
        $txt .= parent::convertClass($className);
        return $txt;
    }
   
    /**
     * end the angular module
     * @param type $className
     * @return string
     */
    protected function endConvertClass($className) {
        return 'return ' . $className. ';'. $this->END_OF_LINE .'}]);';
    }
}
