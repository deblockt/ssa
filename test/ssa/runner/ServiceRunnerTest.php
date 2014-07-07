<?php

namespace ssa\runner;

use ssa\runner\ServiceRunner;
use ssa\runner\resolver\Pojo;
use ssa\ServiceMetadata;
use ssa\runner\resolver\impl\DefaultParameterResolver;
use ssa\runner\converter\annotations\Converter;
use ssa\ServiceManager;
use ssa\Configuration;
use ssa\util\ParameterUtil;

// contact for test default parameter
define('CONST_TEST', 'CONST_TEST_VALUE');

/**
 * Description of ServiceRunnerTest
 *
 * @author thomas
 */
class ServiceRunnerTest extends \PHPUnit_Framework_TestCase{
    
    /**
     * méthode action qui permet de tester le service runner
     * 
     * @param string $param1
     * @param \ssa\runner\resolver\impl\Pojo $param2
     * @param array(int) $intArray
     * 
     * @return array all parameter into an array
     */
    public function service1($param1, Pojo $param2, array $intArray) {
        return array($param1, $param2, $intArray);
    }
    
    /**
     * méthode action qui permet de tester les paramétres par defaut
     * 
     * @param string $param1
     * @param string $param2
     * 
     * @Converter("\ssa\runner\converter\DefaultJsonSerializer")
     * 
     * @return array all parameter into an array
     */
    public function service2($param1 = 'test', $param2 = CONST_TEST) {
        return array($param1, $param2);
    }
    
    /**
     * 
     * @param typeNotSupported $param1
     * @return param
     */
    public function service3($param1) {
        return $param1;
    }
    
    /**
     * 
     * @param string $param1
     * 
     * @Converter("\badClass")
     * 
     * @return param
     */
    public function service4($param1) {
        return $param1;
    }
    
    public function setUp() {
        ServiceManager::getInstance()->registerService('testServiceRunner', 'ssa\runner\ServiceRunnerTest');
        ServiceManager::getInstance()->registerService(
            'testServiceRunnerService1',
            'ssa\runner\ServiceRunnerTest',
            array('service1')
        );
        ServiceManager::getInstance()->registerService(
            'testServiceRunnerService2',
            'ssa\runner\ServiceRunnerTest',
            array('service2')
        );
        ServiceManager::getInstance()->registerService(
            'testServiceRunnerService3',
            'ssa\runner\ServiceRunnerTest',
            array('service3')
        );
        ServiceManager::getInstance()->registerService(
            'testServiceRunnerService4',
            'ssa\runner\ServiceRunnerTest',
            array('service4')
        );
    }
    
    public function testExecuteActionWithDefaultResolver() {
        $serviceRunner = new ServiceRunner('testServiceRunner');
        $parameters = ParameterUtil::explodeParameter(array(
            'param1' => 'test',
            'param2.param' => 'value1',
            'param2.pojo.param' => 'value2',
            'intArray' => array('bla',0,5,'45')
        ));
        $returnJson = $serviceRunner->runAction('service1', $parameters);
        
        $return = json_decode($returnJson);
        
        $this->assertEquals('test', $return[0]);
        $this->assertEquals('value1', $return[1]->param);
        $this->assertEquals('value2', $return[1]->pojo->param);
        $this->assertEquals(array(0,0,5,45), $return[2]);
    }
    
    public function testExecuteActionWithOtherResolver() {
        $serviceRunner = new ServiceRunner(
            'testServiceRunner',
            DefaultParameterResolver::createDefaultParameterResolver()
        );
        $parameters = ParameterUtil::explodeParameter(array(
            'param1' => 'test',
            'param2.param' => 'value1',
            'param2.pojo.param' => 'value2',
            'intArray' => array('bla',0,5,'45')
        ));
        $returnJson = $serviceRunner->runAction('service1', $parameters);
        $return = json_decode($returnJson);        
        $this->assertEquals('test', $return[0]);
        $this->assertEquals('value1', $return[1]->param);
        $this->assertEquals('value2', $return[1]->pojo->param);
        $this->assertEquals(array(0,0,5,45), $return[2]);
    }
    
    public function testExecuteActionMissingParameter() {
        $serviceRunner = new ServiceRunner('testServiceRunner');
        try {
            $serviceRunner->runAction('service1', array());
        } catch (\ssa\runner\MissingParameterException $ex) {
            $this->assertEquals('param1', $ex->getParameterName());
        }
    }
    
    public function testExecuteActionWithDefaultParameter() {
        $serviceRunner = new ServiceRunner('testServiceRunner');
        $returnJson = $serviceRunner->runAction('service2', array());
        $return = json_decode($returnJson);        
        $this->assertEquals('test', $return[0]);
        $this->assertEquals('CONST_TEST_VALUE', $return[1]);
        $returnJson2 = $serviceRunner->runAction('service2', array('param2' => 'testtest'));
        $return2 = json_decode($returnJson2);
        $this->assertEquals('test', $return2[0]);
        $this->assertEquals('testtest', $return2[1]);
    }
    
    
    public function testActionNotSupported() {
        $serviceRunner = new ServiceRunner('testServiceRunnerService1');
        $returnJson = $serviceRunner->runAction('service2', array());
        $return = json_decode($returnJson);
        $this->assertEquals(3001, $return->errorCode);
    }
    
    public function testWithUnsuportedType() {
        $serviceRunner = new ServiceRunner('testServiceRunnerService3');
        $returnJson = $serviceRunner->runAction('service3', array('param1' => 'value1'));
        $return = json_decode($returnJson);
        $this->assertEquals(3101, $return->errorCode);
    }

    public function testCallWithFileCache() {
        $serviceRunner = new ServiceRunner('testServiceRunnerService2');
        Configuration::getInstance()->configure(array(
            'cacheMode' => 'file',
            'cacheDirectory' => __DIR__.'/cache',
            'debug' => true
        ));
        $returnJson = $serviceRunner->runAction('service2');
        $return = json_decode($returnJson);        
        $this->assertEquals('test', $return[0]);
        $this->assertEquals('CONST_TEST_VALUE', $return[1]);
        // test with cache reader
        $returnJson = $serviceRunner->runAction('service2');
        $return = json_decode($returnJson);        
        $this->assertEquals('test', $return[0]);
        $this->assertEquals('CONST_TEST_VALUE', $return[1]);
    }
    
    public function testCallActionWithBadEncoder() {
        $serviceRunner = new ServiceRunner('testServiceRunnerService4');
        $returnJson = $serviceRunner->runAction('service4', array('param1' => 'value1'));
        $return = json_decode($returnJson);
        $this->assertEquals(3200, $return->errorCode);
    }
}
