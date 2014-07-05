<?php

namespace ssa\runner\resolver;

/**
 * A chain of responsabilty (COR) for convert object into correct type
 *  PrimitiveResolverCOR
 *
 * @author thomas
 */
class ObjectResolverCORTest extends \PHPUnit_Framework_TestCase {
    
    private $objectResolverCor;
    private $objectResolverCor2;
    private $objectResolverCor3;
    
    public function setUp() {
        $this->objectResolverCor = $this->getMockForAbstractClass('ssa\runner\resolver\ObjectResolverCor');
        $this->objectResolverCor->expects($this->any())
                                ->method('canResolve')
                                ->will($this->returnValue(false));
        
        $this->objectResolverCor2 = $this->getMockForAbstractClass('ssa\runner\resolver\ObjectResolverCor');
        $this->objectResolverCor2->expects($this->any())
                                ->method('canResolve')
                                ->will($this->returnValue(true));
        $this->objectResolverCor2->expects($this->any())
                                ->method('resolve')
                                ->will($this->returnValue(2));
        
        $this->objectResolverCor3 = $this->getMockForAbstractClass('ssa\runner\resolver\ObjectResolverCor');
        $this->objectResolverCor3->expects($this->any())
                                ->method('canResolve')
                                ->will($this->returnValue(true));
        $this->objectResolverCor3->expects($this->any())
                                ->method('resolve')
                                ->will($this->returnValue(8));
        
    }
    
    /**
     * @expectedException \ssa\runner\resolver\TypeNotSupportedException
     */
    public function testCanNotResolve() {
        $docParam = array('Test');
        $this->objectResolverCor->resolveObject(
                new \ReflectionClass('\PHPUnit_Framework_TestCase'),
                array(),
                $docParam
        );
    }

    public function testUseSecondResolver() {
        $this->objectResolverCor->addResolver($this->objectResolverCor2);
        $docParam = array('Test');
        $return = $this->objectResolverCor->resolveObject(
                new \ReflectionClass('\PHPUnit_Framework_TestCase'),
                array(),
                $docParam
        );
        $this->assertEquals(2, $return);
    }
    
    public function testUseFirstPossibleResolver() {
        $this->objectResolverCor->addResolver($this->objectResolverCor2);
        $this->objectResolverCor->addResolver($this->objectResolverCor3);
        $docParam = array('Test');
        $return = $this->objectResolverCor->resolveObject(
                new \ReflectionClass('\PHPUnit_Framework_TestCase'),
                array(),
                $docParam
        );
        $this->assertEquals(8, $return);
    }
}
