<?php

class Modules_Guitestcase_Framework_Manager
{
    protected $_config;
    protected static $_instance;

    /**
     * Синглтон
     *
     * @return Modules_Guitestcase_Framework_Manager
     */
    public static function getInstance()
    {
        if (null == self::$_instance) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    protected function __construct()
    {
        $this->_config = Zend_Registry::get('config')->Guitestcase;
    }

    /**
     * Поиск классов с тестами
     *
     * @return array
     */
    public function getTestCaseClasses()
    {
        $files = $this->_findFiles();
        $array = array();

        foreach ($files as $file) {
            $array[System_String::StrToLower(System_Functions::File2Class($file))] = System_Functions::File2Class($file);
        }

        return $array;
    }

    /**
     * Запускаем тестирование класса
     *
     * @param PHPUnit_Framework_TestCase $object
     * @return xml
     */
    public function run($object)
    {
        $testCase = new Modules_Guitestcase_Framework_TestCase();

        return $testCase->runTestCase($object);
    }

    
    /**
     * Поиск файлов по поттернам указанным в config.ini
     *
     * @return array
     */
    protected function _findFiles()
    {
        $array = array();
        foreach ($this->_config->search_pattern as $folder) {
            $files = glob($folder, GLOB_NOSORT);
            $array = array_merge($array, $files);
        }

        return $array;
    }
}
