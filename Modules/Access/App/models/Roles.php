<?php

class Modules_Access_Model_Roles extends Zend_Db_Table  {

	protected $_name = 'access_roles';

	/**
	 * Получаем роль по её имени
	 *
	 * @param string $role_name
	 * @return Zend_Db_Row
	 */
	public function getRole($role_name) {
		
		return $this->fetchRow($this->select()->where('name = ?', $role_name));
		
	}

}