<?php

namespace ssa\runner;

use ssa\ServiceMetadata;
/**
 * Description of ServiceRunnerTest
 *
 * @author thomas
 */
class ServiceMetadataTest extends \PHPUnit_Framework_TestCase{
    
    public function testContructWithStringClass() {
        $service = new ServiceMetadata(
            'testPhpUnit',
            '\PHPUnit_Framework_TestCase',
            array()
        );
        $this->assertEquals('testPhpUnit', $service->getServiceName());
        $this->assertEquals('PHPUnit_Framework_TestCase', $service->getClass()->getName());
        $this->assertEquals(array(), $service->getMethods());
    }
    
    public function testContructWithReflectionClass() {
        $service = new ServiceMetadata(
            'testPhpUnit',
            new \ReflectionClass('\PHPUnit_Framework_TestCase'),
            array('assertTrue')
        );
        $this->assertEquals('testPhpUnit', $service->getServiceName());
        $this->assertEquals('PHPUnit_Framework_TestCase', $service->getClass()->getName());
        $this->assertEquals(array('assertTrue'), $service->getMethods());
    }
}
