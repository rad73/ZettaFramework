<?php

/**
 * Абстрактный класс для авторизации пользователя
 *
 * @author Александр Хрищанович
 *
 */
abstract class Modules_Access_Framework_Auth_Plugin_Abstract implements Zend_Auth_Adapter_Interface {

	protected $_userName;
	protected $_hashPassword;

	protected $_errors;
	protected $_resultObject;

	protected $_treatment;


	/**
	 * Авторизация по логину и солёному паролю
	 *
	 * @return bool
	 */
	public function authenticate() {

		if ($this->_userName && $this->_hashPassword) {

			$adapter = new Zend_Auth_Adapter_DbTable(Zend_Registry::get('db'));
			$adapter
				->setTableName($this->_getUsersTableName())
				->setIdentityColumn('username')
				->setCredentialColumn('password')
				->setCredentialTreatment($this->_getTreatment());

			$adapter
				->setIdentity($this->_userName)
				->setCredential($this->_hashPassword);

			$result = Zend_Auth::getInstance()->authenticate($adapter);

			if ($result->isValid()) {
				$this->_resultObject = $adapter;
				return true;
			}
			else {
				Zend_Auth::getInstance()->clearIdentity();
				$this->_errors = $result->getMessages();
				$this->_resultObject = null;
				return false;
			}
		}

	}

	/**
	 * Setter for _userName
	 *
	 * @param string $userName
	 * @return Modules_Access_Framework_Auth_Plugin_Abstract
	 */
	public function setUserName($userName) {
		$this->_userName = $userName;
		return $this;
	}

	/**
	 * Setter for _hashPassword
	 *
	 * @param string $hash
	 * @return Modules_Access_Framework_Auth_Plugin_Abstract
	 */
	public function setHashPassword($hash) {
		$this->_hashPassword = $hash;
		return $this;
	}

	/**
	 * Результирующий объект информации о пользователе при успешном логине
	 *
	 * @return stdClass
	 */
	public function getResultObject() {
		return $this->_resultObject->getResultRowObject(array('username', 'role_name', 'password'));
	}

	/**
	 * Getter for errors;
	 *
	 * @return array
	 */
	public function getErrors() {
		return $this->_errors;
	}

	/**
	 * Название таблицы с пользователями
	 *
	 * @return string
	 */
	protected function _getUsersTableName() {
		$table = new Modules_Access_Model_Users();
		$info = $table->info();
		return $info['name'];
	}

	protected function _getTreatment() {

		$db = Zend_Registry::get('db');

		if ($db instanceof Zend_Db_Adapter_Pdo_Sqlite) {
			return "md5('". Zend_Registry::get('config')->Db->staticSalt. "' || ? || salt) AND active = 1";
		}
		else {

			return "MD5(CONCAT('"
				. Zend_Registry::get('config')->Db->staticSalt. "', ?, salt
			)) AND active = 1";

		}

	}

	public function getAuth() {
		return Zend_Auth::getInstance();
	}

}