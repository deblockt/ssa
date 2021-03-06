<?php

namespace ssa\toEndTest;

use ssa\runner\resolver\Pojo;
use ssa\runner\converter\annotations\Encoder;
use ssa\runner\annotations\RunnerHandler;

/**
 * Description of HelloWorld
 *
 * @author thomas
 */
class HelloWorld {
    
    /**
     * @RunnerHandler
     * @param string $yourName
     * @return string 
     */
    public function helloYou($yourName) {
        return 'Hello ' . $yourName.' !!';
    }
    
    /**
     * @Encoder("\ssa\runner\converter\FileEncoder")
     * 
     * @param file $file1 the file
     */
    public function getFileContent($file1) {
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
