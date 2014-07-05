<?php

namespace ssa\util;

/**
 * Description of ParameterUtil
 *
 * @author thomas
 */
class ParameterUtil {
    
        
    /**
     * convert all parameters into an array
     * @param array $parameteres
     */
    public static function explodeParameter($parameters)
    {
        $return = array();
        
        foreach ($parameters as $parameterName => $value) {
            $param = explode('.', $parameterName);
            $end = &self::createSubArray($return, $param);
            $end = $value;
        }
        
        return $return;
    }
    
    /**
     * create a array 
     * exemple $list = ['param1', 'subparam1', 'subparam2']
     * give 
     * array(
     *   'param1' => array(
     *      'subparam1' => array(
     *          'subparam2' => array()
     *       ) 
     *   )
     * )
     * 
     * @param type $base the base is the created array
     * @param array $list the liste of value
     */
    private static function &createSubArray(&$base,array $list) {
        $count = count($list);
        $currentArray = &$base;
        for ($i = 0; $i < $count - 1; ++$i) {
            $node = $list[$i];            
            if (!isset($currentArray[$node])) {
                $currentArray[$node] = array();
            }
            $currentArray = &$currentArray[$node];
        }
        $currentArray[$list[$count - 1]] = null;
        
        return $currentArray[$list[$count - 1]];
    }
}
