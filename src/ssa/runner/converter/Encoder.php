<?php

namespace ssa\runner\converter;

/**
 * Description of Encoder
 *
 * @author thomas
 */
interface Encoder {
    /**
     * method call for convert $data in a string to return
     * 
     * @param mixed $data data must be encoded
     * 
     * @return string the data encoded
     */
    public function encode($data);
}
