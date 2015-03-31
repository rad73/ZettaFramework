<?php

/**
 * Класс помошник для быстрого доступа к пользователю
 * @author Александр Хрищанович
 * @example 
 * 
 * Modules_Access_Framework_User::getInstance()->getUserName()	// возвращает имя пользователя
 * Modules_Access_Framework_User::getInstance()->getRole()		// возвращает роль пользователя
 *
 */
class Modules_Access_Framework_User {

	protected static $_instance;

	/**
	 * Синглтон
	 *
	 * @return Modules_Access_Framework_User
	 */
	public static function getInstance() {

		if (self::$_instance == null) {
			self::$_instance = new self();
		}

		return self::$_instance;

	}
	
	protected function __construct() {}
	protected function __clone() {}

	/**
	 * Получаем имя пользователя
	 *
	 * @return string
	 */
	public function getUserName() {
		$object = Zend_Auth::getInstance()->getIdentity();
		return is_object($object) ? $object->username : false;
	}

	/**
	 * Получаем роль пользователя
	 *
	 * @return string
	 */
	public function getRole() {
		$object = Zend_Auth::getInstance()->getIdentity();
		return is_object($object) ? $object->role_name : false;
	}
	
}