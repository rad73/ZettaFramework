<?php

class Modules_Menu_Model_Menu extends Zetta_Db_Table  {

	protected $_name = 'menu';

	/**
	 * Модель разелов меню
	 *
	 * @var Modules_Menu_Model_MenuItems
	 */
	protected $_modelItems;


	public function __construct($config = array(), $definition = null) {

		parent::__construct($config, $definition);
		$this->_modelItems = new Modules_Menu_Model_MenuItems();

	}

	/**
	 * Получаем список всех меню
	 *
	 * @return Zend_Db_Rowset
	 */
	public function getAllMenu() {
		return $this->fetchFull();
	}

	/**
	 * Получаем свойства конкретного меню
	 *
	 * @param int $menuId
	 * @return Zend_Db_Row
	 */
	public function getMenu($menuId) {

		foreach($this->fetchFull() as $i=>$row) {

			if ($row->menu_id == $menuId) {
				return $row;
			}

		}

	}


	/**
	 * Получаем дерево разделов определённого меню
	 *
	 * @param int $menuId
	 * @return array
	 */
	public function getMenuTree($menuId) {

		$menu = $this->getMenu($menuId);
		if (!sizeof($menu)) return array();

		$sections = $this->_modelItems->getMenuItems($menu->menu_id);

		if ($menu->type == 'router') {

			$routerTree = Modules_Router_Model_Router::getInstance()->getItem($menu->parent_route_id);

			$_makeTreeFromRouter = function($routerTree, $sections, $parentDisable) use (&$_makeTreeFromRouter) {

				$return = array();

				foreach ($routerTree as $i=>$row) {

					if (1 == $row['disable']) continue;

					$return[$i] = $row;
					$return[$i]['type'] = 'from_router';
					$return[$i]['item_id'] = $row['route_id'];
					$return[$i]['parent_id'] = $row['parent_route_id'];
					$return[$i]['disable'] = $parentDisable;

					unset($return[$i]['route_id'], $return[$i]['parent_route_id'], $return[$i]['childs']);

					if (sizeof($sections)) {
						foreach ($sections as $item) {

							if ($item->route_id == $row['route_id']) {
								$return[$i]['disable'] = $item->disable || $parentDisable;
								$return[$i]['name'] = $item->name ? $item->name : $row['name'];
							}
						}
					}

					$return[$i]['childs'] = $_makeTreeFromRouter($row['childs'], $sections, $return[$i]['disable']);

				}

				return $return;

			};

			$sections = $_makeTreeFromRouter($routerTree['childs'], $sections, false);

		}
		else {

			$_makeUrl = function($sections) use (&$_makeUrl) {

				$return = array();

				foreach ($sections as $i=>$item) {

					$return[$i] = $item;
					if ($item['external_link']) {
						$return[$i]['url'] = $item['external_link'];
					}
					else {
						$return[$i] += Modules_Router_Model_Router::getInstance()->getItem($item['route_id']);
					}

					$return[$i]['childs'] = $_makeUrl($item['childs']);

				}

				return $return;

			};

			$sections = System_Functions::toForest($sections, 'item_id', 'parent_id');
			$sections = $_makeUrl($sections);

		}

		$this->_setCurrents($sections);

		return $sections;

	}

	/**
	 * Добавляем раздел
	 * @param array $data
	 * @return ID вставленной записи
	 */
	public function insertSection(array $data) {
		return $this->_modelItems->insert($data);
	}

	/**
	 * Обновляем раздел
	 * @param array $data
	 * @param array|string $data	SQL WHERE запрос
	 * @return ID обновлённой записи
	 */
	public function updateSection(array $data, $where) {
		return $this->_modelItems->update($data, $where);
	}

	/**
	 * Получаем информацию о разделе
	 *
	 * @param int $item_id
	 * @param int $menu_id
	 *
	 * @return array
	 */
	public function getSection($item_id, $menu_id) {

		$menu = $this->getMenu($menu_id);

		if ($menu->type == 'router') {

			// получаем разделы меню привязонного к маршруту

			$item = $this->_modelItems->fetchRow($this->_modelItems->select()
				->where('route_id = ?', $item_id)
				->where('menu_id = ?', $menu->menu_id)
			);
			$itemRoute = Modules_Router_Model_Router::getInstance()->getItem($item_id);

			$return = $item ? $item->toArray() + $itemRoute: $itemRoute;
			$return['type'] = 'router';
			$return['parent_id'] = $itemRoute['parent_route_id'];
			$return['name_route'] = $itemRoute['name'];
			$return['name'] = $item['name'] ? $item['name'] : $itemRoute['name'];

			unset($return['parent_route_id'], $return['childs']);

		}
		else {
			$return = $this->_modelItems->fetchRow($this->_modelItems->select()->where('item_id = ?', $item_id))->toArray();
		}

		return $return;

	}

	/**
	 * Получаем ассоциативный массив дерева для списков selectbox
	 *
	 * @param int $menu_id
	 * @param bool $ignore_disabled	не выводить отключенные разделы
	 * @return array
	 */
	public function getTreeHash($menu_id, $ignore_disabled = false) {

		$tree = $this->getMenuTree($menu_id);

		$_makeHash = function($parents = false, $level = 0) use (&$_makeHash) {

			$array = array();

			foreach ($parents as $row) {

				if (isset($ignore_disabled) && true == $ignore_disabled && $row['disable']) continue;

				$array[$row['item_id']] = str_repeat('-', $level) . ' ' . $row['item_id'] . ': ' . $row['name'];
				if (sizeof($row['childs'])) {
					$array = $array + $_makeHash($row['childs'], $level + 1);
				}

			}

			return $array;

		};

		return $_makeHash($tree);

	}

	/**
	 * Удаляем элемент меню
	 *
	 * @param int $item_id
	 */
	public function deleteItem($item_id) {

		$where = $this->_modelItems->getAdapter()->quoteInto('item_id = ?', $item_id);
		$this->_modelItems->delete($where);

	}

	/**
	 * Получаем субменю определённого уровня
	 *
	 * @param int $level	Уровень с которого выводить
	 * @return array
	 */
	public function getSubmenu($level) {

		$router = Modules_Router_Model_Router::getInstance();

		$current_route_id = $router->current();

		if ($current_route_id) {

			$current_route_id = $current_route_id['route_id'];

			$tree = $router->getParentsId($current_route_id);
			$tree = array_reverse($tree);

			if (sizeof($tree) > $level) {
				$root_id = $tree[$level];
			}
			else {
				$root_id = $current_route_id;
			}

			$primary_menu = $this->getMenuTree(1);	// в свойствах главного меню лежат разделы которые нужно выключить

			$_getChilds = function($parents, $root_id) use (&$_getChilds) {

				foreach ($parents as $row) {

					if ($row['item_id'] == $root_id) {
						return $row['childs'];
					}
					else {
						$return = $_getChilds($row['childs'], $root_id);
					}

				}

			};

			$childs = $_getChilds($primary_menu, $root_id);
			$this->_setCurrents($childs);

			return $childs;

		}

	}

	/**
	 * Устанавливаем в дереве меню какие разделы выделены
	 *
	 * @param array $tree
	 */
	private $_parents = array();
	protected function _setCurrents(&$tree) {

		if (sizeof($this->_parents) == 0) {
			$router = Modules_Router_Model_Router::getInstance();
			$current = $router->current();
			if ($current) {
				$this->_parents = $router->getParentsId($current['route_id']);
			}
		}

		$currentUrl = Zend_Controller_Front::getInstance()->getRequest()->getPathInfo();
		preg_match('|(.*/)(.*\.html)?|', $currentUrl, $matches);	// отрубаем *.html от пути

		if (sizeof($tree)) {
			foreach ($tree as &$row) {

				if ($row['type'] == 'router' && in_array($row['route_id'], $this->_parents)) {
					$row['current'] = true;
					return true;
				}
				else if (sizeof($matches) > 1 && $row['url'] == $matches[1]) {
					$row['current'] = true;
					return true;
				}
				else {
					$row['current'] = $this->_setCurrents($row['childs']);
				}


			}
		}

		return false;

	}

}