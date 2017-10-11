<?php

class Modules_Admin_Model_Panel extends Zend_Db_Table  {

	protected $_name = 'admin_panel_favorites';

	protected function _findModules() {

		$moduleInfoFiles =
            (array)glob(HEAP_PATH . '/*/info.ini', GLOB_NOSORT)
			+ (array)glob(MODULES_PATH . '/*/info.ini', GLOB_NOSORT);

		sort($moduleInfoFiles);

		$return = array();
		$returnDeveloper = array();
		foreach ($moduleInfoFiles as $row) {

			$config = new Zend_Config_Ini($row);

			preg_match('|.*/(.*)/info.ini$|i', $row, $matches);

			/* регистрируем плагин вывода панели администрирования на frontend */
			if (Zetta_Acl::getInstance()->isAllowed('admin_module_' . System_String::StrToLower($matches[1]), 'deny')) {

				if (false == $config->developer) {

					$return[] = array_merge(
						$config->toArray(),
						array('module' => $matches[1])
					);

				}
				else {

					$returnDeveloper[] = array_merge(
						$config->toArray(),
						array('module' => $matches[1])
					);

				}

			}

		}

		return array($return, $returnDeveloper);

	}

	public function findModules() {
		$modules = $this->_findModules();
		return $modules[0];
	}

	public function findModulesDeveloper() {
		$modules = $this->_findModules();
		return $modules[1];
	}

	public function getFavorites($username) {

		$data = $this->fetchAll(
			$this->select()
				->where('username = ?', $username)
				->order('id')
		);

		$allModules = array_merge($this->findModules(), $this->findModulesDeveloper());
		$return = array();

		foreach ($data as $user_row) {
			foreach ($allModules as $row) {
				if (System_String::StrToLower($row['module']) == System_String::StrToLower($user_row['module'])) {
					$return[] = array_merge(array('id' => $user_row['id']), $row);
				}
			}

		}

		return System_Functions::toObject($return);

	}

}
