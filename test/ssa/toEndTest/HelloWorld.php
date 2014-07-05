<?php

namespace ssa\toEndTest;

use ssa\runner\resolver\Pojo;

/**
 * Description of HelloWorld
 *
 * @author thomas
 */
class HelloWorld {
    
    /**
     * 
     * @param string $yourName
     * @return string 
     */
    public function helloYou($yourName) {
        return 'Hello ' . $yourName.' !!';
    }
    
    /**
     * 
     * @param \ssa\runner\resolver\Pojo $pojo
     * @param array(\ssa\runner\resolver\Pojo) $pojos
     */
    public function returnPojo(Pojo $pojo, array $pojos) {
        return array($pojo, $pojos);
    }
}
