<?php

namespace ssa\runner\resolver\impl;


use ssa\runner\resolver\impl\ArrayFilePrimitiveResolver;

/**
 * Description of ArrayFilePrimitiveResolver
 *
 * @author thomas
 */
class ArrayFilePrimitiveResolverTest extends \PHPUnit_Framework_TestCase {
    /**
     *
     * @var ArrayFilePrimitiveResolver
     */
    private $primitiveResolver;
    
    public function setUp() {
        $this->primitiveResolver = new ArrayFilePrimitiveResolver();
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
        $this->primitiveResolver->resolvePrimitive('test', $type);      
    }
    
    /**
     * @expectedException \ssa\runner\resolver\TypeNotSupportedException
     */
    public function testResolvePrimitiveArrayNotFile() {
        $type = array('array','something');
        $this->primitiveResolver->resolvePrimitive('test', $type);      
    }
    
    public function testResolvePrimitiveArrayPrimitive() {
        $type = array('array', 'file');
        $param = array(
            'error' => array(0,0),
            'tmp_name' => array('',''),
            'size' => array(0,0),
            'name' => array('', ''),
            'type' => array('', '')
        );
        $return = $this->primitiveResolver->resolvePrimitive($param, $type);  
        // 12 12 12 12 à cause du mock
        $this->assertEquals(array(12,12), $return);
    }
    
        
}
