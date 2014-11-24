<?php

namespace ssa\converter;

use ssa\converter\JavascriptConverter;

/**
 * javascript converter for require js support
 * Warning SSA must be defined on require js config
 * 
 * @author Thomas Deblock
 */
class RequireJsJavascriptConverter extends JavascriptConverter {
   /**
     * create require js define
     * 
     * @param type $className
     * @return type
     */
    protected function convertClass($className) {
        $txt = 'define([\'ssa\'], function(ssa){'.$this->END_OF_LINE;
        $txt .= parent::convertClass($className);

        return $txt;
    }
   
    /**
     * end the requirejs module
     * @param type $className
     * @return string
     */
    protected function endConvertClass($className) {
        return 'return ' . $className. ';'. $this->END_OF_LINE .'});';
    }
} 
