<?php

namespace ssa\runner\resolver;

/**
 * Description of Pojo
 *
 * @author thomas
 */
class Pojo {
    private $param;
    
    private $pojo;
    
    public function getParam() {
        return $this->param;
    }
    /**
     * 
     * @param string $param
     */
    public function setParam($param) {
        $this->param = $param;
    }


    public function getPojo() {
        return $this->pojo;
    }

    /**
     * 
     * @param \ssa\runner\resolver\Pojo $pojo
     */
    public function setPojo($pojo) {
        $this->pojo = $pojo;
    }


    
}
