<?php

namespace ssa\converter;

use ssa\converter\ServiceConverter;
use ssa\converter\UrlFactory;
use ssa\Configuration;

/**
 * Description of JavascriptConverter
 *
 * @author thomas
 */
class JavascriptConverter extends ServiceConverter {
    private $END_OF_LINE = "\n" ;
    
    private $debug = true;
    
    /**
     * {@inheritdoc}
     */
    public function __construct( $serviceName, UrlFactory $urlFactory) {
        parent::__construct($serviceName, $urlFactory);
        // configure default debug with configuration class
        $this->debug = Configuration::getInstance()->getDebug();
    }
    
    /**
     * enable or disable debug mode
     * @param boolean $debug
     */
    public function setDebug($debug) {
        $this->debug = $debug;
    }
    
    
    protected function convertClass($className) {
        return 'var '.$className.' = {};'.$this->END_OF_LINE;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function convertMethod($methodName, $params, $comment) {        
        $url = $this->constructUrl($this->getServiceName().'.'.$methodName);
        
        $return = '';
        if ($this->debug) {
            $return = $comment.$this->END_OF_LINE;
        }
        $return .= $this->getServiceName() .'.' . $methodName . ' = function(';
        $paramAsJson = '{';
        foreach ($params as $name => $type) {
            $return .= $name ;   
            $paramAsJson .= '\''. $name . '\' : ' . $name ;
            if (next($params) == true) {
                $paramAsJson .= ',';
                $return .= ', ';
            }
        } 
        $paramAsJson .= '}';
        // add all function parameter
        $return .= ') {'. $this->END_OF_LINE;
        $return .= $this->tabulate(1).
                    'return ssa.call(\''.$url.'\', '.$paramAsJson.', false);'
                    .$this->END_OF_LINE;
        // add call code
        $return .= '}; '.$this->END_OF_LINE;
        
        return $return;
    }

    /**
     * return number of tabulation
     * @param int $number
     */
    protected function tabulate($number) {
        if (!$this->debug) {
            return '';
        }
        $return  = '';
                
        for ($i = 0; $i < $number; $i++) {
            $return .= "\t";
        }
        
        return $return;
    }




}
