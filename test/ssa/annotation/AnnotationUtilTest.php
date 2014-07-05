<?php

namespace ssa\annotation;

use ssa\annotation\AnnotationUtil;

/**
 * Description of AnnotationUtil
 *
 * @author thomas
 */
class AnnotationUtilTest extends \PHPUnit_Framework_TestCase 
{
    /**
     * test le cas du type sans paramétres
     * exemple :
     * array
     * date
     * string
     */
    public function testSplitParameterWithNoParameter() {
        $return = AnnotationUtil::splitParameter('array');
        $expected = array('array');
        $this->assertEquals($expected, $return);
    }
    
    /**
     * test le cas du type avec paramétres
     * exemple :
     * array(\test\FOO)
     * \DateTime(d/m/Y)
     */
    public function testSplitParameterWithWithParameter() {
        $return = AnnotationUtil::splitParameter('array(\test\Foo)');
        $expected = array('array', '\test\Foo');
        $this->assertEquals($expected, $return);
        $returnObject = AnnotationUtil::splitParameter('\DateTime(d/m/Y)');
        $expectedObject = array('\DateTime', 'd/m/Y');
        $this->assertEquals($expectedObject, $returnObject);
    }
    
    /**
     * test la lecture des paramétre dans un bloc de commentaire
     */
    public function testGetMethodParameters() {
        $types = AnnotationUtil::getMethodParameters('
            /**
             * test
             *
             * @param array             $param1 with comment
             * @param \DateTime (  d/m/y  ) $param2
             * @param mixed $param3
             * @param array(  \test\Foo ) $param4 le commentaire
             */
                ');
        $expected = array(
            'param1' => array('array'),
            'param2' => array('\DateTime', 'd/m/y'),
            'param3' => array('mixed'),
            'param4' => array('array', '\test\Foo')
        );
        $this->assertEquals($expected, $types);
    }
}
