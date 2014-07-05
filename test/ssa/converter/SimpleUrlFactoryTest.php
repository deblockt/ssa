<?php

namespace ssa\converter;

use ssa\converter\SimpleUrlFactory;

/**
 * Description of SimpleUrlFactoryTest
 *
 * @author thomas
 */
class SimpleUrlFactoryTest extends \PHPUnit_Framework_TestCase {
    
    public function testUrlReplace() {
        $urlFactory = new SimpleUrlFactory('http://foo.bar/action={action}');
        $this->assertEquals(
            'http://foo.bar/action=service.test',
            $urlFactory->constructUrl('service.test')
        );        
    }
}
