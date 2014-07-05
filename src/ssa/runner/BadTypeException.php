<?php

namespace ssa\runner;

/**
 * Description of ParameterMandatory
 *
 * @author thomas
 */
class BadTypeException extends \Exception {
    private $expectedType;
    private $realType;
    private $paramName;
    
    public function __construct($paramName, $expectedType, $realType) {
        parent::__construct(
            'bad type for parameter '.$paramName.' expected '.$expectedType. ' give '.$realType,
            3100
        );
        $this->expectedType = $expectedType;
        $this->realType = $realType;
        $this->paramName = $paramName;
    }
    
    public function getExcpectedType() {
        return $this->expectedType;
    }
    
    public function getRealType() {
        return $this->realType;
    }
    
    public function getParamName() {
        return $this->paramName;
    }
    
    public function setParamName($paramName) {
        $this->paramName = $paramName;
        $this->message = 'bad type for parameter '.$this->paramName.' expected '.$this->expectedType. ' give '.$this->realType;
    }
}
