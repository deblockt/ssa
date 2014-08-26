<?php

namespace ssa\runner\converter;

use ssa\runner\converter\DefaultJsonEncoderTest;


/**
 * 
 * 
 * Description of DefaultJsonEncoderTest
 *
 * @author thomas
 */
class DefaultJsonEncoderTest extends \PHPUnit_Framework_TestCase {
   
    private $encoder;
    
    public function setUp() {
        $this->encoder = new DefaultJsonEncoder();
    }
    
    public function testArrayEncoder() {
        $result = $this->encoder->encode(array(1,2,'3'));
        $this->assertEquals('[1,2,"3"]', $result);
        $this->assertEquals(array(
            'Content-type' => 'application/json'
        ), $this->encoder->getHeaders());
    }
    
    public function testArrayKeyValueEncoder() {
        $result = $this->encoder->encode(array(
            'param1' => 'value1',
            'param2' => (object) array('param1' => 'value2'),
            'param3' => array('bla'),
            'param4' => 10
        ));
        // attribute without getter are not exported
        $this->assertEquals('{"param1":"value1","param2":[],"param3":["bla"],"param4":10}', $result);
        $this->assertEquals(array(
            'Content-type' => 'application/json'
        ), $this->encoder->getHeaders());
    }
    
    public function testObjectEncoder() {
        $pojo = new Pojo();
        $subPojo = new Pojo();
        $subPojo->setParam1('sub-value');
        $pojo->setParam1('value1');
        $pojo->setParam2(154);
        $pojo->setParam3($subPojo);
        
        $result = $this->encoder->encode($pojo);
        $this->assertEquals('{"param1":"value1","param3":{"param1":"sub-value","param3":null}}', $result);
        $this->assertEquals(array(
            'Content-type' => 'application/json'
        ), $this->encoder->getHeaders());
    }
   
    public function testArrayObjectEncoder() {
        $pojo = new Pojo();
        $pojo->setParam1('value1');
        $pojo->setParam2(154);
        $pojo->setParam3(48);
        $pojo2 = new Pojo();
        $pojo2->setParam1('sub-value');
        
        $result = $this->encoder->encode(array($pojo, $pojo2));
        $this->assertEquals('[{"param1":"value1","param3":48},{"param1":"sub-value","param3":null}]', $result);
        $this->assertEquals(array(
            'Content-type' => 'application/json'
        ), $this->encoder->getHeaders());
    }
}

class Pojo {
    private $param1;
    private $param2;
    private $param3;
    
    public function getParam1() {
        return $this->param1;
    }

    public function getParam3() {
        return $this->param3;
    }

    public function setParam1($param1) {
        $this->param1 = $param1;
    }

    public function setParam3($param3) {
        $this->param3 = $param3;
    }
    
    public function setParam2($param2) {
        $this->param2 = $param2;
    }
}
