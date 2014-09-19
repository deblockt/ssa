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
    protected $END_OF_LINE = "\n" ;
    
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
            $return = $this->constructJsComment($params, $comment).$this->END_OF_LINE;
        }
        $return .= $this->getServiceName() .'.' . $methodName . ' = function(';
        $paramAsJson = '{';
        $haveFileType = false;
        foreach ($params as $name => $type) {
            $return .= $name ;   
            $paramAsJson .= '\''. $name . '\' : ' . $name ;
            if (next($params) == true) {
                $paramAsJson .= ',';
                $return .= ', ';
            }
            if ($this->mustUseFormData($type)) {
                $haveFileType = true;
            }
        } 
        $paramAsJson .= '}';
        // add all function parameter
        $return .= ') {'. $this->END_OF_LINE;
        $return .= $this->tabulate(1).
                    'return ssa.call(\''.$url.'\', '.$paramAsJson.', false, '.($haveFileType ? 'true' : 'false').');'
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

    /**
     * construct the js comment
     * 
     * @param array $params
     * @param string $comment
     */
    public function constructJsComment($params, $inputComment) {
        $parameters = $this->getMethodParameters($inputComment);
        $comment = '/**'.$this->END_OF_LINE;
        $methodComment = $this->getMethodComment($inputComment);
        $methodReturn = $this->getMethodReturn($inputComment);
        
        foreach ($methodComment as $commentLine) {
            $comment .= ' '.$commentLine.$this->END_OF_LINE;
        }
        foreach ($params as $name => $type) {
            $comment .= ' * @param '.$this->implodeParameterType($type).' '.$name;
            if (isset($parameters[$name])) {
                $comment .= ' '.$parameters[$name]['comment'];
            }
            $comment .= $this->END_OF_LINE;
        }
        
        if ($methodReturn != null) {
            $comment .= ' * '.$this->END_OF_LINE
                       .' * @return '.$methodReturn.$this->END_OF_LINE;
        }
        
        return $comment.' */';
    }
    
    /**
     * exctract the first bloc comment
     * before param definition
     * 
     * @param string $doc the doc comment
     * 
     * @return array each ligne of the doc
     */
    protected function getMethodComment($doc) {
        preg_match_all('#\/\*\*(.*)(\*\s+@param|\*\s+@return|\**/)#isU', $doc, $annotations);
        if (!isset($annotations[1][0])) {
            return array();
        }
        $lignesNotTrim = explode("\n", $annotations[1][0]);
        
        $return = array();
        foreach ($lignesNotTrim as $ligne) {
            $trimLine = trim($ligne);
            if ($trimLine != '') {
                $return[] = $trimLine;
            }
        }
        
        return $return;
    }
    
    /**
     * get the return value from the method
     * 
     * @param string $doc
     */
    protected function getMethodReturn($doc) {
        preg_match_all('#@return(.*)\n#isU', $doc, $annotations);
        if (!isset($annotations[1][0])) {
            return null;
        }
        return $annotations[1][0];
    }
    
    /**
     * fonction pour retourner les types des arguments
     * récupération des types grâce aux commentaires
     * 
     * @param string $doc le commentaire de la fonction
     * @return array la liste des types pour chaque paramétre
     */
    protected function getMethodParameters($doc) {
        // récupération des type et des noms des variables
        preg_match_all('#@param\s+(.+)\s+\$([^\s]+)(.*)[\n|\*]#i', $doc, $annotations);
        
        $return = array();
        $count = count($annotations[2]);
        for ($i = 0; $i < $count; $i++) {
            $return[trim($annotations[2][$i])] = array(
                'type' => $annotations[1][$i],
                'comment' => $annotations[3][$i]
            );
        }
        return $return;
    }
    
    /**
     * function return true if this type must use form data
     * 
     * @param array $type
     */
    protected function mustUseFormData(array $types) {
        foreach ($types as $type) {
            if ($type == 'file') {
                return true;
            }
        }
        
        return false;
    }
    
    protected function implodeParameterType($typeExploded) {
        $types = implode('(', $typeExploded);
        $count = count($typeExploded);
        
        for ($i = 1; $i < $count; $i++) {
            $types .= ')';
        }
        
        return $types;
    }

}
