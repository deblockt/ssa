<?php

namespace ssa\runner\resolver\impl;

use ssa\runner\resolver\impl\FilePrimitiveResolver;
/**
 * Description of FilePrimitiveResolver
 *
 * @author thomas
 */
class FilePrimitiveResolverTest extends \PHPUnit_Framework_TestCase  {
    private $primitiveResolver;

    public function setUp() {
        $this->primitiveResolver = new FilePrimitiveResolver();
    }


    public function testResolvePrimitiveSimpleArray() {
        $type = array('file');
        $param = array(
            'error' => array(0,1),
            'tmp_name' => array('2','3'),
            'size' => array(4,5),
            'name' => array('6', '7'),
            'type' => array('8', '9')
        );
        $return = $this->primitiveResolver->resolvePrimitive($param, $type);
        $this->assertEquals(array(
                'error' => 0,
                'tmp_name' => '2',
                'size' => 4,
                'name' => '6',
                'type' => '8'
            ),
            $return
        );   
    }
}
