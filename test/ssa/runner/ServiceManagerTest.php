<?php

namespace ssa\runner;

use ssa\ServiceManager;

/**
 * Description of ServiceRunnerTest
 *
 * @author thomas
 */
class ServiceManagerTest extends \PHPUnit_Framework_TestCase{
    /**
     *
     * @var ServiceManager
     */
    private $serviceManager;
    
    public function setUp() {
        $this->serviceManager = ServiceManager::getInstance();
    }
    
    
    public function testRegisterAllService() {
        $this->serviceManager->registerAllServices(array(
           'service1' => array(
                'class' => 'ssa\runner\ServiceManagerTest',
                'methods' => array('method1')
           ),
           'service2' => array(
                'class' => 'ssa\runner\ServiceMetadataTest',
                'methods' => array()
           ),
        ));
        
        $this->assertEquals(
            'ssa\runner\ServiceManagerTest',
            $this->serviceManager->getService('service1')->getClass()->getName()
        );
        $this->assertEquals(
            'ssa\runner\ServiceMetadataTest',
            $this->serviceManager->getService('service2')->getClass()->getName()
        );
        $this->assertEquals(array(), $this->serviceManager->getService('service2')->getMethods());
        $this->assertEquals(array('method1'), $this->serviceManager->getService('service1')->getMethods());
        
    }

    /**
     * @expectedException \ssa\ServiceNotRegistredException
     */
    public function testAccessToUnregisterService() {
        $this->serviceManager->getService('foo');
    }
}
