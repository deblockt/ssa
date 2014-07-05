<?php

namespace ssa\converter;

use ssa\converter\UrlFactory;

/**
 * Description of SimpleUrlFactory
 *
 * @author thomas
 */
class SimpleUrlFactory implements UrlFactory {
    
    /**
     *
     * @var string 
     */
    private $templateUrl;
    
    /**
     * 
     * @param string $templateUrl the url with {action} paramater http://exemple.com/?action={action}
     * 
     */
    public function __construct($templateUrl) {
        $this->templateUrl = $templateUrl;
    }
    /**
     * construct url, replace {action} by the action
     * 
     * @param string $action the action
     */
    public function constructUrl($action) {
        return str_replace('{action}', $action, $this->templateUrl);
    }

}
