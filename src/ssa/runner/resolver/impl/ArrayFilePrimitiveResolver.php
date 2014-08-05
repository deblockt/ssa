<?php

namespace ssa\runner\resolver\impl;

use ssa\runner\resolver\impl\ArrayPrimitiveResolver;

/**
 * resolver for array(file)
 * return an array
 * 
 * array(
 *  [0] => array (
 *    'name' => string
 *    'size' => int
 *    'error' => int
 *    'tmp_name' => string
 *    'type' => string
 *  )
 *  [1] => array(
 *    'name' => string
 *    'size' => int
 *    'error' => int
 *    'tmp_name' => string
 *    'type' => string
 *   )
 * )
 * @author thomas
 */
class ArrayFilePrimitiveResolver extends ArrayPrimitiveResolver {
    /**
     * return true for array(file)
     * 
     * @param array $type
     * @return type
     */
    protected function canResolve(array &$type) {
        return count($type) > 1 && $type[0] == 'array' && $type[1] == 'file';
    }

    protected function resolve($value, array &$type) {
        $return = array();
        $count = count($value['error']);
        
        for ($i = 0; $i < $count; $i++) {
            $return[] = array(
                'error' => array($value['error'][$i]),
                'name' => array($value['name'][$i]),
                'size' => array($value['size'][$i]),
                'tmp_name' => array($value['tmp_name'][$i]),
                'type' => array($value['type'][$i])
            );
        }
        
        return parent::resolve($return, $type);
    }
}
