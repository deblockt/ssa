<?php

namespace ssa\runner\resolver\impl;

use ssa\runner\resolver\impl\ArrayPrimitiveResolver;

/**
 * Description of ArrayPrimitiveResolverTest
 *
 * @author thomas
 */
class ArrayPrimitiveResolverTest extends \PHPUnit_Framework_TestCase {
    
    private $primitiveResolver;
    
    public function setUp() {
        $this->primitiveResolver = new ArrayPrimitiveResolver();
        $parameterResolver = $this->getMock('ssa\runner\resolver\ParameterResolver',
                                   array('resolvePrimitive','resolveObject')
                                );
        $parameterResolver->expects($this->any())
                          ->method('resolvePrimitive')
                          ->will($this->returnValue(12));
        $parameterResolver->expects($this->any())
                          ->method('resolveObject')
                          ->will($this->returnValue((object) array('para1' => 'value1')));
        
        $this->primitiveResolver->setParameterResolver($parameterResolver);
    }
    
    /**
     * @expectedException \ssa\runner\resolver\TypeNotSupportedException
     */
    public function testResolvePrimitiveSimpleArray() {
        $type = array('array');
        $return = $this->primitiveResolver->resolvePrimitive('test', $type);      
    }
    
    public function testResolvePrimitiveArrayPrimitive() {
        $type = array('array', 'integer');
        $param = array('12','bla',8,15.5);
        $return = $this->primitiveResolver->resolvePrimitive($param, $type);  
        // 12 12 12 12 à cause du mock
        $this->assertEquals(array(12,12,12,12), $return);
    }
    
    public function testResolvePrimitiveArrayObjectNotExists() {
        $type = array('array', 'ObjectNotExists');
        $param = array('12','bla',8,15.5);
        $return = $this->primitiveResolver->resolvePrimitive($param, $type);  
        // 12 12 12 12 à cause du mock
        $this->assertEquals(array(12,12,12,12), $return);
    }
        
    public function testResolvePrimitiveArrayObject() {
        $type = array('array', '\ssa\runner\resolver\Pojo');
        $param = array('12','bla',8,15.5);
        $return = $this->primitiveResolver->resolvePrimitive($param, $type);  
        foreach ($return as $object) {
            $this->assertTrue(\is_object($object));
        }
    }
}
