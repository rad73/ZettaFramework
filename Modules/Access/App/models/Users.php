<?php

class Modules_Access_Model_Users extends Zetta_Db_Table  {

	protected $_name = 'access_users';

	protected $_acceptedRoles;

	public function __construct($config = array(), $definition = null) {

		parent::__construct($config, $definition);
		$this->_acceptedRoles = array_keys(Zetta_Acl::getInstance()->getAccepdedRolesHash());

	}

	/**
	 * Генерируем соль
	 *
	 * @return string
	 */
	public static function GenerateSalt() {

		$md5Rand = md5(microtime());
		$randLength = rand(5, strlen($md5Rand));

		return substr($md5Rand, 0, $randLength);

	}

	public function getUsersInRole($role_name, $order = 'username') {

		if (in_array($role_name, $this->_acceptedRoles)) {
			return $this->fetchAll($this->select()
				->where('role_name = ?', $role_name)
				->order($order)
			);
		}

	}

	public function getUsers($order = 'username') {

		if (sizeof($this->_acceptedRoles)) {

			return $this->fetchAll($this->select()
				->where('role_name IN (?)', $this->_acceptedRoles)
				->order($order)
			);

		}

	}

	public function getUser($username) {

		return $this->fetchRow($this->select()
			->where('username = ?', $username)
		);

	}

	public function deleteUser($login) {

		$user = $this->getUser($login);

		if (in_array($user->role_name, $this->_acceptedRoles)) {
			$this->delete($this->getAdapter()->quoteInto('username = ?', $login));
		}

	}

}
