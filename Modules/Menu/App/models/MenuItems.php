<?php

class Modules_Menu_Model_MenuItems extends Zetta_Db_Table  {

	protected $_name = 'menu_items';

	private static $_fullData = null;

	public function fetchFull() {

		if (null === self::$_fullData) {

			self::$_fullData = $this->fetchAll($this->select()
				->order('sort', 'item_id')
			);

		}

		return self::$_fullData;

	}

	/**
	 * Получаем разделы меню
	 *
	 * @param int $menuId
	 * @return array
	 */
	public function getMenuItems($menuId) {

		$returnArray = array();

		foreach($this->fetchFull() as $i=>$row) {

			if ($row->menu_id == $menuId) {
				array_push($returnArray, $row);
			}

		}

		return $returnArray;

	}


}