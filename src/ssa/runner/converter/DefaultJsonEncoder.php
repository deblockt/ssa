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
     * @return string
     */
    public function encode($data) {
        $this->serializableData = $data;
        return json_encode($this);
    }

    /**
     * create an array who can be convert in json
     * 
     * @param \ssa\runner\converter\Traversable $data
     * @return \ssa\runner\converter\Traversable
     */
    private function serialize($data) {
        $return = null;
        if(is_array($data) ||  $data instanceof Traversable ) {
            $return = array();
            foreach ($data as $key => $value) {
                $return[$key] = $this->serialize($value);
            }
        } else if (is_object ($data)) {
            // récupération des getter de la classe
            // TODO mettre les méthodes en cache par rapport aux classes
            $return = array();
            $reflectionClass = new \ReflectionClass($data);
            foreach ($reflectionClass->getMethods() as $method) {
                if(stripos($method->getName(), 'get')!==FALSE && count($method->getParameters()) == 0){
                     $property = lcfirst(mb_substr($method->getName(), 3,mb_strlen($method->getName(),'UTF-8'),'UTF-8'));
                     $return[$property] = $this->serialize($method->invoke($data));
                }
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
