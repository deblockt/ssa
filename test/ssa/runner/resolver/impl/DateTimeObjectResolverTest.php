<?php

namespace ssa\runner\resolver\impl;

use ssa\runner\resolver\impl\DateTimeObjectResolver;

class DateTimeObjectResolverTest extends \PHPUnit_Framework_TestCase {

    private $ojectResolver;

    public function setUp() {
        $this->ojectResolver = new DateTimeObjectResolver();
    }

    public function testResolveObject() {
        $type = array('\DateTime', 'm/d/Y');
        $return = $this->ojectResolver->resolveObject(
                new \ReflectionClass('\DateTime'), '05/10/2014', $type
        );
        $this->assertEquals(10, $return->format('d'));
        $this->assertEquals(05, $return->format('m'));
        $this->assertEquals(2014, $return->format('Y'));
        $this->assertEquals(14, $return->format('y'));
    }

    public function testResolveObjectDefaultDateFormat() {
        $type = array('\DateTime');
        $return = $this->ojectResolver->resolveObject(
                new \ReflectionClass('\DateTime'), '12/31/2014 11:05:25', $type
        );
        $this->assertEquals(31, $return->format('d'));
        $this->assertEquals(12, $return->format('m'));
        $this->assertEquals(2014, $return->format('Y'));
        $this->assertEquals(11, $return->format('H'));
        $this->assertEquals(05, $return->format('i'));
        $this->assertEquals(25, $return->format('s'));
    }

    /**
     * @expectedException \ssa\runner\resolver\TypeNotSupportedException
     */
    public function testResolveObjectWithNoCorectDate() {
        $type = array('\DateTime', 'm/d/Y');
        $this->ojectResolver->resolveObject(
                new \ReflectionClass('\DateTime'), '05/505/000', $type
        );
    }

    /**
     * @expectedException \ssa\runner\resolver\TypeNotSupportedException
     */
    public function testResolveObjectWithNoCorectDateFormat() {
        $type = array('\DateTime', 'm/d/jdhY');
        $this->ojectResolver->resolveObject(
                new \ReflectionClass('\DateTime'), '05/15/2014', $type
        );
    }
    
}
