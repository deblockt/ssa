<?php

namespace ssa\toEndTest;

use ssa\runner\resolver\Pojo;
use ssa\runner\converter\annotations\Converter;

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
     * 
     * @param file $file1 the file
     * @param file $file2 
     */
    public function getFileContent($file1, $file2) {
        return $file1;
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
