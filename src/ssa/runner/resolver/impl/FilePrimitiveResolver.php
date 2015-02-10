<?php

namespace ssa\runner\resolver\impl;

use ssa\runner\resolver\PrimitiveResolverCOR;

/**
 * Resolver file type get the first file in the list
 * if you ave multiple file use array(file)
 * 
 * file is an array as $_FILES
 * array (
 *  'name' => string
 *  'size' => int
 *  'error' => int
 *  'tmp_name' => string
 *  'type' => string
 * );
 * 
 * the parameter must be a file
 *
 * @author thomas
 */
class FilePrimitiveResolver extends PrimitiveResolverCOR {
    
    protected function canResolve(array &$type) {
        return isset($type[0]) && $type[0] == 'file';
    }

    protected function resolve($value, array &$type) {
        $return = array();
        $return['name'] = $value['name'][0];
        $return['size'] = $value['size'][0];
        $return['tmp_name'] = $value['tmp_name'][0];
        $return['error'] = $value['error'][0];
        $return['type'] = $value['type'][0];
        return $return;
    }
}
