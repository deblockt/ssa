<?php

namespace ssa\converter;

use ssa\ServiceManager;
/**
 * Description of JavascriptConverterTest
 *
 * @author thomas
 */
class JavascriptConverterTest extends \PHPUnit_Framework_TestCase  {
    
    private $urlFactory;
    
    private $url = 'http://test.com/action';
    
    public function setUp() {
        $this->urlFactory = $this->getMock(
            'ssa\\converter\\UrlFactory',
            array('constructUrl')
        );
        $this->urlFactory->expects($this->at(0))
                         ->method('constructUrl')
                         ->will($this->returnValue($this->url));
        
        ServiceManager::getInstance()->registerService('testService', 'ssa\converter\ServiceTest');
        ServiceManager::getInstance()->registerService(
            'testServiceAction2',
            'ssa\converter\ServiceTest',
            array('action2')             
        );
    }
    
    
    public function testJavascriptConverterWithoutMethods() {
        $converter = new JavascriptConverter('testService', $this->urlFactory);
        $converter->setDebug(false);
        $javascript = $converter->convert();
        
        $this->assertTrue(
            strpos($javascript, 'testService.action1 = function(param1, param2)') >= 0,
            'la fonction action1 n\'est pas présente'
        ); 
        $this->assertTrue(
            strpos($javascript, 'testService.action2 = function(param1)') >= 0,
            'la fonction action2 n\'est pas présente'
        ); 
        $this->assertTrue(strpos($javascript, $this->url) >= 0);
        
    }
    
    public function testJavascriptConverterWithMethods() {
        $converter = new JavascriptConverter('testServiceAction2', $this->urlFactory);
        $converter->setDebug(false);
        $javascript = $converter->convert();
        
        $this->assertTrue(
            strpos($javascript, 'testService.action1 = function(param1, param2)') == 0,
            'la fonction action1 ne devrait pas être présente'
        ); 
        $this->assertTrue(
            strpos($javascript, 'testService.action2 = function(param1)') >= 0,
            'la fonction action2 n\'est pas présente'
        ); 
        $this->assertTrue(strpos($javascript, $this->url) >= 0);
    }
    
    public function testJavascriptConverterWithMethodsDebug() {
        $converter = new JavascriptConverter('testServiceAction2', $this->urlFactory);
        $converter->setDebug(true);
        $javascript = $converter->convert();
        
        $this->assertTrue(
            strpos($javascript, 'testService.action1 = function(param1, param2)') == 0,
            'la fonction action1 ne devrait pas être présente'
        ); 
        $this->assertTrue(
            strpos($javascript, 'testService.action2 = function(param1)') >= 0,
            'la fonction action2 n\'est pas présente'
        ); 
        $this->assertTrue(strpos($javascript, $this->url) >= 0);
    }

    public function testJavascriptConverterWithoutMethodsDebug() {        
        $converter = new JavascriptConverter('testService', $this->urlFactory);
        $converter->setDebug(true);
        $javascript = $converter->convert();
        
        $this->assertTrue(
            strpos($javascript, 'testService.action1 = function(param1, param2)') >= 0,
            'la fonction action1 n\'est pas présente'
        ); 
        $this->assertTrue(
            strpos($javascript, 'testService.action2 = function(param1)') >= 0,
            'la fonction action2 n\'est pas présente'
        ); 
        $this->assertTrue(strpos($javascript, $this->url) >= 0);
    }
}
