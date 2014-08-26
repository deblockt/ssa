<?php

namespace ssa\runner\converter;

use ssa\runner\converter\FileEncoder;

/**
 * Description of FileEncoderTest
 *
 * @author thomas
 */
class FileEncoderTest extends \PHPUnit_Framework_TestCase {
    
    private $encoder;
    
    public function setUp() {
        $this->encoder = new FileEncoder();
    }
    
    /**
     * test encoder for a file on an array  
     */
    public function testArrayFileEncoder() {
        $result = $this->encoder->encode(array(
            'type' => 'text/php',
            'tmp_name' => __FILE__
        ));
        $this->assertEquals(file_get_contents(__FILE__), $result);
        $this->assertEquals(array(
            'Content-type' => 'text/php'
        ), $this->encoder->getHeaders());
    }
    
    public function testFileEncoderWithString() {
        $result = $this->encoder->encode(__FILE__);
        $this->assertEquals(file_get_contents(__FILE__), $result);
        $this->assertEquals(array(
            'Content-type' => mime_content_type(__FILE__)
        ), $this->encoder->getHeaders());
    }
}
