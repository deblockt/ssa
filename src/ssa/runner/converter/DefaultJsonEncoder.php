<?php

namespace ssa\runner\converter;

use ssa\runner\converter\Encoder;

/**
 * Description of DefaultJsonSerialize
 *
 * @author thomas
 */
class DefaultJsonEncoder implements \JsonSerializable, Encoder {
    private $serializableData;
    
    /**
     *
     * @var array vart list of non serializable element
     */
    private $excludePath = null;
    
    /**
     *
     * @var array all serialized objects
     */
    private $alreadySerialized = array();
    
    /**
     * 
     * @param \ssa\runner\converter\annotations\Encoder $encoder the encoder annotation
     */
    public function __construct(annotations\Encoder $encoder = null) {
        if ($encoder != null) {
            $this->excludePath = $encoder->excludePath;
        }      
    }
    
    
    /**
     * method used by json_encode
     * @return type
     */
    public function jsonSerialize () {        
        return $this->serialize($this->serializableData);
    }
    
    /**
     * return json
     * 
     * @param mixed $data
     * @param array $excludePath the list of exclude path
     * @return string
     */
    public function encode($data, $excludePath = null) {
        $this->serializableData = $data;
        return json_encode($this);
    }

    /**
     * return true if the path can be added on the return 
     * 
     * @param type $path
     * @return type
     */
    private function canBeAdded($path) {
        return $this->excludePath == null || $this->excludePath != null && !in_array($path, $this->excludePath);
    }
    
    /**
     * create an array who can be convert in json
     * 
     * @param \ssa\runner\converter\Traversable $data
     * @return \ssa\runner\converter\Traversable
     * 
     * 
     */
    private function serialize($data, $path = null, $alreadySerialized = array()) {
        // TODO gérer les cycles, ajouter un paramétre permettre de ne pas exporter certains champs
        $return = null;
        if(is_array($data) ||  $data instanceof \Traversable ) {
            $return = array();
            foreach ($data as $key => $value) {
                $newPath = $path . (($path == null) ? '' : '.') . (is_int($key) ? '' : $key);
                if ($this->canBeAdded($newPath)) {
                    $return[$key] = $this->serialize($value, $newPath, $alreadySerialized);
                }
            }
        } else if (is_object ($data)) {
            if (in_array($data, $alreadySerialized)) {
                return "cyclical_dependencies";
            }
            
            $alreadySerialized[] = $data;
            
            // récupération des getter de la classe
            // TODO mettre les méthodes en cache par rapport aux classes
            $return = array();
            $reflectionClass = new \ReflectionClass($data);
            
            foreach ($reflectionClass->getMethods() as $method) {
                if(stripos($method->getName(), 'get')!==FALSE && count($method->getParameters()) == 0){
                    $property = lcfirst(mb_substr($method->getName(), 3,mb_strlen($method->getName(),'UTF-8'),'UTF-8'));
                    $newPath = $path . (($path == null) ? '' : '.') . $property;
                    if ($this->canBeAdded($newPath)) {
                        $return[$property] = $this->serialize($method->invoke($data), $newPath, $alreadySerialized);
                    }
                }
            }
        } else if (is_string($data)) {
            if ('UTF-8' != mb_detect_encoding($data)) {
                $return = utf8_encode($data);
            } else {
                $return = $data;
            }
        } else {
            $return = $data;
        }
        return $return;
    }

    /**
     * {@inherits}
     */
    public function getHeaders() {
        return array(
            'Content-type' => 'application/json'
        );
    }

}
