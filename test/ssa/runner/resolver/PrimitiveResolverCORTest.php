<?php

namespace ssa\runner\resolver;

/**
 * A chain of responsabilty (COR) for convert object into correct type
 *  PrimitiveResolverCOR
 *
 * @author thomas
 */
class PrimitiveResolverCORTest extends \PHPUnit_Framework_TestCase {
    
    private $primitiveResolverCor;
    private $primitiveResolverCor2;
    private $primitiveResolverCor3;
    
    public function setUp() {
        $this->primitiveResolverCor = $this->getMockForAbstractClass('ssa\runner\resolver\PrimitiveResolverCor');
        $this->primitiveResolverCor->expects($this->any())
                                ->method('canResolve')
                                ->will($this->returnValue(false));
        
        $this->primitiveResolverCor2 = $this->getMockForAbstractClass('ssa\runner\resolver\PrimitiveResolverCor');
        $this->primitiveResolverCor2->expects($this->any())
                                ->method('canResolve')
                                ->will($this->returnValue(true));
        $this->primitiveResolverCor2->expects($this->any())
                                ->method('resolve')
                                ->will($this->returnValue(2));
        
        $this->primitiveResolverCor3 = $this->getMockForAbstractClass('ssa\runner\resolver\PrimitiveResolverCor');
        $this->primitiveResolverCor3->expects($this->any())
                                ->method('canResolve')
                                ->will($this->returnValue(true));
        $this->primitiveResolverCor3->expects($this->any())
                                ->method('resolve')
                                ->will($this->returnValue(8));
        
    }
    
    /**
     * @expectedException \ssa\runner\resolver\TypeNotSupportedException
     */
    public function testCanNotResolve() {
        $docParam = array('Test');
        $this->primitiveResolverCor->resolvePrimitive(
                12,
                $docParam
        );
    }

    public function testUseSecondResolver() {
        $this->primitiveResolverCor->addResolver($this->primitiveResolverCor2);
        $docParam = array('Test');
        $return = $this->primitiveResolverCor->resolvePrimitive(
                12,
                $docParam
        );
        $this->assertEquals(2, $return);
    }
    
    public function testUseFirstPossibleResolver() {
        $this->primitiveResolverCor->addResolver($this->primitiveResolverCor2);
        $this->primitiveResolverCor->addResolver($this->primitiveResolverCor3);
        $docParam = array('Test');
        $return = $this->primitiveResolverCor->resolvePrimitive(
                12,
                $docParam
        );
        $this->assertEquals(8, $return);
    }
}
