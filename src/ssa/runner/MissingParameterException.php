<?php

namespace ssa\runner;

/**
 * Description of MissingParameterException
 *
 * @author thomas
 */
class MissingParameterException extends \Exception {
   private $parameterName;
   
   public function __construct($paramName) {
        parent::__construct('the parameter '. $paramName .' is mandatory', 3101);
        $this->parameterName = $paramName;
   }
   
   public function getParameterName() {
       return $this->parameterName;
   }


}
