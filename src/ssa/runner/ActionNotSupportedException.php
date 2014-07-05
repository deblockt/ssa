<?php

namespace ssa\runner;

/**
 * Description of ActionNotSupportedException
 *
 * @author thomas
 */
class ActionNotSupportedException extends \Exception {
    
    public function __construct($action) {
        parent::__construct($action, 3001);
    }
}
