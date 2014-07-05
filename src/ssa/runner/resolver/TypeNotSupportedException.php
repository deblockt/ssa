<?php

namespace ssa\runner\resolver;

/**
 * Description of ParameterMandatory
 *
 * @author thomas
 */
class TypeNotSupportedException extends \Exception {
    private $varname;
    private $type;
    
    public function __construct($type) {
        parent::__construct('type '.$type.' not supported for var ', 3101);
        $this->setType($type);        
    }
    
    public function getVarname() {
        return $this->varname;
    }

    public function getType() {
        return $this->type;
    }

    public function setVarname($varname) {
        $this->varname = $varname;
        $this->message = 'type '.$this->type.' not supported for var '.$this->varname;
    
    }

    public function setType($type) {
        $this->type = $type;
    }

    
}
