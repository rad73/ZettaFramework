<?php

class Zetta_TestCase_ZettaBootstrap extends PHPUnit_Framework_TestCase
{

    /**
     * Тестируемая модель
     *
     * @var Modules_Guitestcase_Bootstrap
     */
    protected $_model;
    
    protected function setUp()
    {
        $this->_model = new Modules_Guitestcase_Bootstrap();
    }

    public function testBootstrap()
    {
        $this->_model->bootstrap();
        
        $this->assertObjectHasAttribute('Guitestcase', Zend_Registry::get('config'));
        $this->assertTrue(Zend_Registry::get('config')->Guitestcase instanceof Zend_Config);
    }
    
    public function testGetModulePrefix()
    {
        $this->_model->bootstrap();
        $this->assertEquals($this->_model->getModulePrefix(), 'Modules_');
    }
    
    public function testGetModulePath()
    {
        $this->_model->bootstrap();
        $this->assertEquals($this->_model->getModulePath(), MODULES_PATH . DS . 'Guitestcase');
    }
    
    public function testGetModuleName()
    {
        $this->_model->bootstrap();
        $this->assertEquals($this->_model->getModuleName(), 'Guitestcase');
    }
}
