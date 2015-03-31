<?php
/**
 * Хранение данных от авторизации в куках
 * 
 * @author Александр Хрищанович
 *
 */
class Modules_Access_Framework_Auth_Storage_Cookie implements Zend_Auth_Storage_Interface
{
    /**
     * Default session namespace
     */
    const LIFE_TIME = 86400;

    const NAMESPACE_DEFAULT = 'Zend_Auth';


    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @return boolean
     */
    public function isEmpty()
    {
		return !isset($_COOKIE[self::NAMESPACE_DEFAULT]);
    }

    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @return mixed
     */
    public function read()
    {
    	if (array_key_exists(self::NAMESPACE_DEFAULT, $_COOKIE)) {
        	return unserialize($_COOKIE[self::NAMESPACE_DEFAULT]);
    	}
    }

    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @param  mixed $contents
     * @return void
     */
    public function write($contents) {
    	
    	if (true == is_object($contents)) {

    		$writeClass = new stdClass();
	    	$writeClass->username = $contents->username;
	    	$writeClass->auth_hash = $contents->password;
	    	$writeClass->role_name = $contents->role_name;
	    	
	    	$_COOKIE[self::NAMESPACE_DEFAULT] = serialize($writeClass);
	    	
	    	setcookie(
	    		self::NAMESPACE_DEFAULT,
	            $_COOKIE[self::NAMESPACE_DEFAULT],
	            time() + self::LIFE_TIME,
	            Zend_Controller_Front::getInstance()->getBaseUrl() . DS
			);
			
    	}

    }

    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @return void
     */
    public function clear() {
    	
        setcookie(
    		self::NAMESPACE_DEFAULT,
            false,
            315554400,
			Zend_Controller_Front::getInstance()->getBaseUrl() . DS
		);
        
        unset($_COOKIE[self::NAMESPACE_DEFAULT]);
    }
}
