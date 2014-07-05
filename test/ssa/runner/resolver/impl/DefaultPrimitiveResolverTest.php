<?php

namespace ssa\runner\resolver\impl;

use ssa\runner\resolver\impl\DefaultPrimitiveResolver;

/**
 * Description of DefaultTypeResolver
 *
 * @author thomas
 */
class DefaultPrimitiveResolverTest extends \PHPUnit_Framework_TestCase {

    private $primitiveResolver;

    public function setUp() {
        $this->primitiveResolver = new DefaultPrimitiveResolver();
    }


    public function testResolvePrimitiveSimpleArray() {
        $type = array('array');
        $return = $this->primitiveResolver->resolvePrimitive('test', $type);
        $this->assertEquals(array('test'), $return);
        $return2 = $this->primitiveResolver->resolvePrimitive(array('test'), $type);
        $this->assertEquals(array('test'), $return2);
    }
    
    public function testResolvePrimitive() {        
        $type = array('string');
        $return = $this->primitiveResolver->resolvePrimitive(123, $type);
        $this->assertEquals('123',$return);
        $type2 = array('int');
        $return2 = $this->primitiveResolver->resolvePrimitive('123', $type2);
        $this->assertEquals(123,$return2);
    }
}
