<?php

class Modules_Settings_Model_Settings extends Zend_Config_Ini  {

	/**
	 * Обёект одиночка
	 *
	 * @var Modules_Settings_Model_Settings
	 */
	protected static $_instance = null;
	
	protected $_fileConfig;
	
	public function __construct() {
		$this->_fileConfig = FILE_PATH . DS . 'Configs' . DS . 'site.ini';
		parent::__construct($this->_fileConfig, null, array('allowModifications' => true));
	}
	
	public static function getInstance() {
		
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
		
	}
	
	public function save($array, $key) {
		
		$this->_data[$key] = new Zend_Config($array, true);
		
		$writer = new Zend_Config_Writer_Ini();
		$writer->write($this->_fileConfig, $this);
		
	}
	
	public function delete($key) {
		
		unset($this->$key);
		
		$writer = new Zend_Config_Writer_Ini();
		$writer->write($this->_fileConfig, $this);
		
	}
	
	public function __get($name) {
		
		$object = $this->get($name);
		
		if ($object) {
			return $object->value;
		}
		
		return false;
    }
    
    public function __set($name, $value) {

    	$object = $this->get($name);
    	
		if ($object) {

			$array = $object->toArray();
			$array['value'] = $value;
			
			$this->save($array, $name);

		}

    }
    
}