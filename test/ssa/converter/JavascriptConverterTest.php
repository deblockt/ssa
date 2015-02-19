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
    
    private $url = 'http://test.com/action/2';
    
    public function setUp() {
        $this->urlFactory = new SimpleUrlFactory('http://test.com/action/2');

        ServiceManager::getInstance()->registerService('testService', 'ssa\converter\ServiceTest');
        ServiceManager::getInstance()->registerService('testExtendsService', 'ssa\converter\ExtendedServiceTest');
        ServiceManager::getInstance()->registerService(
            'testServiceAction2',
            'ssa\converter\ServiceTest',
            array('action2', 'action3')             
        );
    }
    
    
    public function testJavascriptConverterWithoutMethods() {
        $converter = new JavascriptConverter(
            ServiceManager::getInstance()->getService('testService'),
            $this->urlFactory
        );
        $converter->setDebug(false);
        $javascript = $converter->convert();
        
        $this->assertTrue(
            strpos($javascript, 'testService.action1 = function(param1, param2)')  !== FALSE,
            'la fonction action1 n\'est pas présente'
        ); 
        $this->assertTrue(
            strpos($javascript, 'testService.action2 = function(param1)')  !== FALSE,
            'la fonction action2 n\'est pas présente'
        ); 
        
        $this->assertTrue(
                strpos($javascript, 'console.log(\'coucou\')') !== FALSE,
                'l\'ajout du js ne fonctionne pas'
        );
        $this->assertTrue(strpos($javascript, $this->url) !== FALSE);
        
    }
	
    public function testJavascriptConverterEtendsWithoutMethods() {
        $converter = new JavascriptConverter(
            ServiceManager::getInstance()->getService('testExtendsService'),
            $this->urlFactory
        );
        $converter->setDebug(false);
        $javascript = $converter->convert();
        
        $this->assertTrue(
            strpos($javascript, 'testExtendsService.action1 = function(param1, param2)')  !== FALSE,
            'la fonction action1 n\'est pas présente'
        ); 
        $this->assertTrue(
            strpos($javascript, 'testExtendsService.action2 = function(param1)') !== FALSE,
            'la fonction action2 n\'est pas présente'
        ); 
        $this->assertTrue(
                strpos($javascript, 'console.log(\'coucou\')') !== FALSE,
                'l\'ajout du js ne fonctionne pas'
        );
        
        $this->assertTrue(
                strpos($javascript, 'console.log(\'coucou2\')') !== FALSE,
                'l\'ajout du js ne fonctionne pas'
        );
        $this->assertTrue(strpos($javascript, $this->url) !== FALSE);
        
    }
    
    public function testJavascriptConverterWithMethods() {
        $converter = new JavascriptConverter('testServiceAction2', $this->urlFactory);
        $converter->setDebug(false);
        $javascript = $converter->convert();
        
        $this->assertFalse(
            strpos($javascript, 'testServiceAction2.action1 = function(param1, param2)'),
            'la fonction action1 ne devrait pas être présente'
        ); 
        $this->assertTrue(
            strpos($javascript, 'testServiceAction2.action2 = function(param1)') !== FALSE,
            'la fonction action2 n\'est pas présente'
        ); 
                
        $this->assertTrue(
            strpos($javascript, 'testServiceAction2.action3 = function(service)') !== FALSE,
            'la fonction action3 n\'as pas les bons paramétres'
        ); 
        
        $this->assertFalse(
            strpos($javascript, 'testServiceAction2.action3 = function(param1)'),
            'la fonction action3 n\'as pas les bons paramétres'
        ); 
        $this->assertTrue(strpos($javascript, $this->url) >= 0);
    }
    
    public function testJavascriptConverterWithMethodsDebug() {
        $converter = new JavascriptConverter('testServiceAction2', $this->urlFactory);
        $converter->setDebug(true);
        $javascript = $converter->convert();
        
        $this->assertFalse(
            strpos($javascript, 'testServiceAction2.action1 = function(param1, param2)'),
            'la fonction action1 ne devrait pas être présente'
        ); 
        $this->assertTrue(
            strpos($javascript, 'testServiceAction2.action2 = function(param1)')  !== FALSE,
            'la fonction action2 n\'est pas présente'
        ); 
        $this->assertTrue(strpos($javascript, $this->url)  !== FALSE);
    }

    public function testJavascriptConverterWithoutMethodsDebug() {        
        $converter = new JavascriptConverter('testService', $this->urlFactory);
        $converter->setDebug(true);
        $javascript = $converter->convert();
        
        $this->assertTrue(
            strpos($javascript, 'testService.action1 = function(param1, param2)')  !== FALSE,
            'la fonction action1 n\'est pas présente'
        ); 
        $this->assertTrue(
            strpos($javascript, 'testService.action2 = function(param1)')  !== FALSE,
            'la fonction action2 n\'est pas présente'
        ); 
        $this->assertTrue(strpos($javascript, $this->url)  !== FALSE);
    }
}
