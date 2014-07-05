<?php

namespace ssa\converter;

/**
 * Description of UrlFactory
 *
 * @author thomas
 */
interface UrlFactory {
    /**
     * construct an URL for execute the acti
     * 
     * @param string $action the action
     * 
     * @return string the url
     */
    public function constructUrl($action);
}
