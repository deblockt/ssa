<?php

namespace ssa;

/**
 * Description of ServiceNotRegistredException
 *
 * @author thomas
 */
class ServiceNotRegistredException extends \Exception {
  
    public function __construct($message) {
        parent::__construct($message, 3000);
    }
}
