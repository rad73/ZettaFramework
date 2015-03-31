<?php

class Modules_Menu_AdminController extends Zend_Controller_Action {

	/**
	 * Модель Modules_Menu_Model_Menu
	 *
	 * @var Modules_Menu_Model_Menu
	 */
	protected $_modelMenu;
	
	public function init() {

		if (false == Zetta_Acl::getInstance()->isAllowed('admin_module_menu')) {
			throw new Exception('Access Denied');
		}
		
		$this->_modelMenu = new Modules_Menu_Model_Menu();

		$this->_helper->getHelper('AjaxContext')
        	->addActionContext('index', 'html')
        	->addActionContext('tree', 'html')
        	->addActionContext('add', 'html')
        	->addActionContext('addsection', 'html')
        	->addActionContext('delete', 'json')
        	->addActionContext('deletesection', 'json')
        	->addActionContext('savetree', 'json')
            ->initContext();

	}

	public function indexAction() {
		$this->view->menu = $this->_modelMenu->getAllMenu();
	}
	
	public function addAction() {
		
		$form = new Zetta_Form(Zend_Registry::get('config')->Menu->form);

		$parentMenuId = $form->getElement('parent_route_id');
		$parentMenuId->addMultiOptions(Modules_Router_Model_Router::getInstance()->getRoutesTreeHash());
		
		if ($menu_id = $this->getParam('menu_id')) {
			$this->view->menu_id = $menu_id;
			$editRouteData = $this->_modelMenu->getMenu($menu_id)->toArray();
			$form->setDefaults($editRouteData);
		}
		
		if (!sizeof($_POST) || !$form->isValid($_POST)) {
		    $this->view->form = $form;
		}
		else {
			
			$arrayData = array(
				'name'	=> $form->getValue('name'),
				'type'	=> $form->getValue('type'),
				'parent_route_id'	=> new Zend_Db_Expr('NULL'),
			);
			
			if ($arrayData['type'] == 'router') {
				$arrayData['parent_route_id'] = intval($form->getValue('parent_route_id'));
			}
			
			if ($menu_id) {
				$this->_modelMenu->update($arrayData, $this->_modelMenu->getAdapter()->quoteInto('menu_id = ?', $menu_id));
			}
			else {
				$this->_modelMenu->insert($arrayData);
			}
			
			$this->renderScript('admin/addComplete.ajax.phtml');

		}
		
	}
	
	public function deleteAction() {
		
		if ($this->getRequest()->isPost() && $menu_id = $this->getParam('menu_id')) {
			$this->_modelMenu->delete($this->_modelMenu->getAdapter()->quoteInto('menu_id = ?', $menu_id));
		}
		
	}
	
	public function treeAction() {
		
		$menu = $this->_modelMenu->getMenu($this->getParam('menu_id'));
		
		if ($menu->menu_id) {
			
			$tree = $this->_modelMenu->getMenuTree($menu->menu_id);
			
			$this->view->menu = $menu;
			$this->view->tree = $tree;
			
		}
		else {
			$this->_forward('index');
		}
		
	}

	public function addsectionAction() {
		
		$menu = $this->_modelMenu->getMenu($this->getParam('menu_id'));
		$this->view->menu_id = $menu->menu_id;
		
		if ('router' == $menu->type) {
			
			$form = new Zetta_Form(Zend_Registry::get('config')->Menu->formSectionRouter);
			
			$item_id = $this->view->item_id = $this->getParam('item_id');
			$editRouteData = $this->_modelMenu->getSection($item_id, $menu->menu_id);
			
			$form->setDefaults($editRouteData);
			
			if (!sizeof($_POST) || !$form->isValid($_POST)) {
			    $this->view->form = $form;
			}
			else {
				
				if (
					(!$form->getValue('name') || $form->getValue('name') == $editRouteData['name_route'])
					&& intval($form->getValue('disable')) == 0
				) {
					$this->_modelMenu->deleteItem($editRouteData['item_id']);
				}
				else {
					
					$arrayData = array(
						'menu_id'	=> $menu->menu_id,
						'parent_id'	=> $pId = intval($form->getValue('parent_id')) ? $pId : new Zend_Db_Expr('NULL'),
						'name'		=> (!$form->getValue('name') || $form->getValue('name') == $editRouteData['name_route']) ? new Zend_Db_Expr('NULL') : $form->getValue('name'),
						'type'		=> 'router',
						'disable'		=> intval($form->getValue('disable')),
						'route_id'	=> $item_id,
					);
					
					if (array_key_exists('item_id', $editRouteData) && $editRouteData['item_id'] != $item_id) {
						$this->_modelMenu->updateSection($arrayData, $this->_modelMenu->getAdapter()->quoteInto('item_id = ?', $editRouteData['item_id']));
					}
					else {
						$this->_modelMenu->insertSection($arrayData);
					}
					
				}
					
				$this->renderScript('admin/addItemComplete.ajax.phtml');
				
			}
			
		}
		else {
		
			$form = new Zetta_Form(Zend_Registry::get('config')->Menu->formSection);
			
			$menuIdElement = $form->getElement('route_id');
			$menuIdElement->addMultiOptions(Modules_Router_Model_Router::getInstance()->getRoutesTreeHash());
			
			$arrayParents = array('0' => '') + $this->_modelMenu->getTreeHash($menu->menu_id);
			$parentIdElement = $form->getElement('parent_id');
			$parentIdElement->addMultiOptions($arrayParents);
			if ($pId = $this->getParam('parent_id')) {
				$parentIdElement->setValue($pId);
			}
			
			if ($item_id = $this->getParam('item_id')) {
				$this->view->item_id = $item_id;
				$editRouteData = $this->_modelMenu->getSection($item_id, $menu->menu_id);
				$form->setDefaults($editRouteData);
			}
			
			if (!sizeof($_POST) || !$form->isValid($_POST)) {
			    $this->view->form = $form;
			}
			else {
				
				$arrayData = array(
					'menu_id'	=> intval($this->getParam('menu_id')),
					'parent_id'	=> ($pId = intval($form->getValue('parent_id'))) ? $pId : new Zend_Db_Expr('NULL'),
					'name'		=> $form->getValue('name'),
					'type'		=> $form->getValue('type_section'),
					'disable'		=> intval($form->getValue('disable')),
					'route_id'	=> new Zend_Db_Expr('NULL'),
					'external_link'	=> new Zend_Db_Expr('NULL'),
				);
				
				if ($arrayData['type'] == 'router') {
					$arrayData['route_id'] = intval($form->getValue('route_id'));
				}
				else {
					$arrayData['external_link'] = $form->getValue('external_link');
				}
				
				if ($item_id) {
					$this->_modelMenu->updateSection($arrayData, $this->_modelMenu->getAdapter()->quoteInto('item_id = ?', $item_id));
				}
				else {
					$this->_modelMenu->insertSection($arrayData);
				}
				
				$this->renderScript('admin/addItemComplete.ajax.phtml');
	
			}
			
		}
		
	}
	
	public function deletesectionAction() {
		
		if ($this->getRequest()->isPost() && $item_id = $this->getParam('item_id')) {
			$this->_modelMenu->deleteItem($item_id);
		}
		
	}
	
	public function savetreeAction() {
		
		if ($this->getRequest()->isPost()) {
		
			foreach ($this->getParam('tree') as $row) {
	
				$where = $this->_modelMenu->getAdapter()->quoteInto('item_id = ?', $row['id']);
	
				$this->_modelMenu->updateSection(array(
					'parent_id'		=> $row['parent_id'] ? $row['parent_id'] : new Zend_Db_Expr('NULL'),
					'sort'			=> $row['sort'],
				), $where);
	
			}
			
		}

	}

}