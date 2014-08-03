<?php

namespace ssa\converter;

/**
 * Service utilisé dans le test des converteurs
 *
 * @author thomas
 */
class ServiceTest {
    
    /**
     * action1 documentation
     * 
     * @param string $param1
     * @param array(array(int)) $param2 second parameter
     */
    public function action1($param1, $param2) {
        
    }
    
    /**
     * 
     * @param string $param1 
     * 
     * @return string the string
     */
    public function action2($param1) {
        
    }
    
    /**
     * action 3 documentation
     * 
     * @param string $param1
     */
    public function action3(ServiceTest $service) {
        
    }
    
    /**
     * documentation avec un return 
     * 
     * @return
     */
    public function action4() {
        return ''; 
    }
    
    /**
     * documentation toute simple
     */
    public function action5() {
    }
}
