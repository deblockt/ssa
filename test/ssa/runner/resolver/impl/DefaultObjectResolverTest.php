<?php

namespace ssa\runner\resolver\impl;

use ssa\runner\resolver\impl\DefaultObjectResolver;
/**
 * Description of DefaultObjectResolver
 *
 * @author thomas
 */
class DefaultObjectResolverTest extends \PHPUnit_Framework_TestCase {
    private $objectResolver;
    
    public function setUp() {
        $this->objectResolver = new DefaultObjectResolver();
        $parameterResolver = $this->getMock('ssa\runner\resolver\ParameterResolver',
                                   array('resolvePrimitive','resolveObject')
                                );
        $parameterResolver->expects($this->any())
                          ->method('resolvePrimitive')
                          ->will($this->returnValue(12));
        $parameterResolver->expects($this->any())
                          ->method('resolveObject')
                          ->will($this->returnValue((object) array('param1' => 'value1')));
        
        $this->objectResolver->setParameterResolver($parameterResolver);
    }
    
    public function testresolveObect() {
        $type = array('\ssa\runner\resolver\Pojo');
        $object = $this->objectResolver->resolveObject(
            new \ReflectionClass('\ssa\runner\resolver\Pojo'),
            array(
                'param' => 12,
                'pojo' => array(
                    'param' => 13
                )
            ), 
            $type
        );
        // not corresponding the parameters because mock
        $this->assertEquals(12, $object->getParam());
        $this->assertEquals('value1', $object->getPojo()->param1);
    }
}
