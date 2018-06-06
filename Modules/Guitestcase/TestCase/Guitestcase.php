<?php

class Modules_Guitestcase_TestCase_Guitestcase extends PHPUnit_Framework_TestCase
{
    protected $_testCaseObject;


    public function setUp()
    {
        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();

        $this->_testCaseObject = new Modules_Guitestcase_Framework_TestCase();
    }

    public function tearDown()
    {
        unset($this->_testCaseObject);
    }

    public function appBootstrap()
    {
    }

    /**
     * Проверка на то что класс-форматер устанавливаетс правильно
     *
     */
    public function testFormatter()
    {
        $this->_testCaseObject->setFormatter(new PHPUnit_Util_Log_JUnit());
        $this->assertTrue($this->_testCaseObject->getFormatter() instanceof PHPUnit_Framework_TestListener);

        $this->_testCaseObject->setDefaultFormatter();
        $this->assertTrue($this->_testCaseObject->getFormatter() instanceof PHPUnit_Util_Log_JUnit);
    }

    /**
     * Проверка на то что класс-обработчик результатов установлен верно
     *
     */
    public function testTestResult()
    {
        $this->_testCaseObject->setTestResult(new PHPUnit_Framework_TestResult());
        $this->assertTrue($this->_testCaseObject->getTestResult() instanceof PHPUnit_Framework_TestResult);

        $this->_testCaseObject->setDefaultTestResult();
        $this->assertTrue($this->_testCaseObject->getTestResult() instanceof PHPUnit_Framework_TestResult);
    }

    /**
     * Тестируем получение массива найденых тестовых классов
     *
     */
    public function testGetTestCases()
    {
        $this->_testCaseObject->setTestCases(array(
            'Modules_Guitestcase_TestCase_Guitestcase',
            '_SomeHiddenTest'
        ));

        $this->assertTrue(in_array('Modules_Guitestcase_TestCase_Guitestcase', $this->_testCaseObject->getTestCases()));
    }
}
