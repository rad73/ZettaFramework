<?php

require_once 'PHPUnit/Util/Log/XML.php';
require_once 'PHPUnit/Framework/TestSuite.php';


abstract class Modules_Guitestcase_Framework_TestCase_Abstract {

	/**
	 * Тестовые классы
	 *
	 * @var array
	 */
	protected $_testCases = array();

	protected $_formatter = null;
	protected $_testResult = null;


	public function __construct() {

		$this
			->setDefaultFormatter()
			->setDefaultTestResult();

	}

	/**
	 * Setter для self::$_testCases
	 *
	 * @param array $testCases
	 */
	public function setTestCases($testCases) {
		$this->_testCases = $testCases;
	}

	/**
	 * Getter для self::$_testCases
	 *
	 * @return array
	 */
	public function getTestCases() {
		return $this->_testCases;
	}

	/**
	 * Установка класса по выводу результатов тестирования
	 *
	 * @param PHPUnit_Framework_TestListener $formatter
	 */
	public function setFormatter(PHPUnit_Framework_TestListener $formatter) {
		$this->_formatter = $formatter;
	}

	/**
	 * Установка класса вывода по умолчанию (System_Testcase_Formatter)
	 *
	 */
	public function setDefaultFormatter() {
		$this->setFormatter(new PHPUnit_Util_Log_XML());
		return $this;
	}

	/**
	 * Извлекаем класс вывода
	 *
	 * @return PHPUnit_Framework_TestListener
	 */
	public function getFormatter() {

		if (null == $this->_formatter) {
			$this->setDefaultFormatter();
		}

		return $this->_formatter;	

	}

	/**
	 * Устанавливаем класс обработки тестов
	 *
	 * @param PHPUnit_Framework_TestResult $testResult
	 */
	public function setTestResult(PHPUnit_Framework_TestResult $testResult) {
		$this->_testResult = $testResult;
		return $this;
	}

	/**
	 * Получение класса обработки тестов
	 *
	 * @return PHPUnit_Framework_TestResult $testResult
	 */
	public function getTestResult() {

		if (null == $this->_testResult) {
			$this->setDefaultTestResult();
		}
		
		return $this->_testResult;

	}

	/**
	 * Устанавливаем класс обработки тестов по умолчанию (PHPUnit_Framework_TestResult)
	 *
	 * @param PHPUnit_Framework_TestResult $testResult
	 */
	
	public function setDefaultTestResult() {
		$this->setTestResult(new PHPUnit_Framework_TestResult());
		return $this;
	}

	public function runTestCase(PHPUnit_Framework_TestCase $testCase) {

		$suite = new PHPUnit_Framework_TestSuite();
		$suite->addTestSuite(get_class($testCase));

		$this->_testResult->addListener($this->getFormatter());
		$result = $suite->run($this->_testResult);

		return $this->getFormatter();

	}
	
}