<?php

class Modules_Router_AdminController extends Zend_Controller_Action {

	/**
	 * Модель Modules_Router_Model_Router
	 *
	 * @var Modules_Router_Model_Router
	 */
	protected $_modelRoutes;
	
	public function init() {

		if (false == Zetta_Acl::getInstance()->isAllowed('admin_module_router')) {
			throw new Exception('Access Denied');
		}
		
		$this->_modelRoutes = Modules_Router_Model_Router::getInstance();
		
		$this->_helper->getHelper('AjaxContext')
        	->addActionContext('index', 'html')
        	->addActionContext('add', 'html')
        	->addActionContext('savetree', 'json')
        	->addActionContext('delete', 'json')
        	->addActionContext('getmoduleactions', 'json')
            ->initContext();

	}
	
	public function indexAction() {
		$this->view->tree = $this->_modelRoutes->getRoutesTree();
	}
	
	public function savetreeAction() {
		
		foreach ($this->getParam('tree') as $row) {

			$where = $this->_modelRoutes->getAdapter()->quoteInto('route_id = ?', $row['id']);

			$this->_modelRoutes->update(array(
				'parent_route_id'	=> $row['parent_id'],
				'sort'				=> $row['sort'],
			), $where);

		}

	}
	
	public function deleteAction() {
		
		if ($route_id = $this->getParam('route_id')) {
		
			$where = $this->_modelRoutes->getAdapter()->quoteInto('route_id = ?', $route_id);
			$this->_modelRoutes->delete($where);
			
		}

	}
	
	public function addAction() {
		
		$form = new Zetta_Form(Zend_Registry::get('config')->Router->form);
		
		$parentMenuId = $form->getElement('parent_route_id');
		$parentMenuId->addMultiOptions($this->_modelRoutes->getRoutesTreeHash());
		
		$defaultActions = $form->getElement('default_modules');
		$defaultActions->addMultiOptions($this->_modelRoutes->getDefaultModules());
		
		if ($pId = $this->getParam('parent_route_id')) {
			$parentMenuId->setValue($pId);
		}
		
		if ($route_id = $this->getParam('route_id')) {
			$this->view->route_id = $route_id;
			$editRouteData = $this->_modelRoutes->getItem($route_id);
			$form->setDefaults($editRouteData);
			
			$exist_modules = array_keys($this->_modelRoutes->getDefaultModules());
			
			if ($editRouteData['module'] != 'default' && !in_array($editRouteData['module'] . '~' . $editRouteData['controller'], $exist_modules)) {
				$type = $form->getElement('type');
				$type->setValue('free');
			}
			else {
				$defaultActions->setValue($editRouteData['module'] . '~' . $editRouteData['controller']);
				$form->addElement('hidden', 'action_value', array('value' => $editRouteData['module'] . '~' . $editRouteData['controller'] . '~' . $editRouteData['action']));
			}
			
			if ($route_id == 1) {
				$form->removeElement('uri');
			}
			
		}
		
		$selectActionsObject = $form->getElement('default_actions');
		$selectActionsObject->setRegisterInArrayValidator(false);
		
		
		if (!sizeof($_POST) || !$form->isValid($_POST)) {
		    $this->view->form = $form;
		}
		else {
			
			$arrayData = array(
				'name'	=> $form->getValue('name'),
				'parent_route_id'	=> $route_id == 1 ? new Zend_Db_Expr('NULL') : intval($form->getValue('parent_route_id')),
				'uri'		=> $route_id == 1 ? '' : $form->getValue('uri'),
				'disable'	=> (bool)$form->getValue('disable'),
				'module'	=> $form->getValue('module'),
				'controller'	=> $form->getValue('controller'),
				'action'	=> $form->getValue('action'),
				'parms'		=> $form->getValue('parms'),
			);
			
			if ($route_id) {
				$this->_modelRoutes->update($arrayData, $this->_modelRoutes->getAdapter()->quoteInto('route_id = ?', $route_id));
			}
			else {
				$this->_modelRoutes->insert($arrayData);
			}
			
			$this->renderScript('admin/addComplete.ajax.phtml');

		}
		
	}
	
	public function getmoduleactionsAction() {
		
		if ($this->getRequest()->isPost() && $this->getParam('_module') && $this->getParam('_controller')) {
			
			$this->view->actions = $this->_modelRoutes->getDefaultActions($this->getParam('_module'), $this->getParam('_controller'));
			
		}
		
	}

}