<?php

namespace ssa\runner;

/**
 * Description of ClassNotFoundException
 *
 * @author thomas
 */
class ClassNotFoundException extends \Exception {
    
    public function __construct($class) {
        parent::__construct($class.' not found', 3200);
    }
}
