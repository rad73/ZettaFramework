<?php

class Modules_Access_AdminController extends Zend_Controller_Action {

	/**
	 * Модель ресурсов
	 *
	 * @var Modules_Access_Model_Resources
	 */
	protected $_modelResources;
	protected $_modelRules;
	protected $_modelRoles;

	
	public function init() {

		if (false == Zetta_Acl::getInstance()->isAllowed('admin_module_access')) {
			throw new Exception('Access Denied');
		}
		
		$this->_modelResources = new Modules_Access_Model_Resources();
		$this->_modelRules = new Modules_Access_Model_Rules();
		$this->_modelRoles = new Modules_Access_Model_Roles();
		
		$this->_helper->getHelper('AjaxContext')
        	->addActionContext('index', 'html')
        	->addActionContext('privelegies', 'html')
        	->addActionContext('delete', 'json')
        	->addActionContext('add', 'html')
        	->addActionContext('roles', 'html')
        	->addActionContext('sortroles', 'json')
        	->addActionContext('roledelete', 'json')
        	->addActionContext('roleadd', 'html')
        	->addActionContext('rulesbyrole', 'html')
            ->initContext();
		
	}

	public function indexAction() {
		
		$acceptedRoles = Zetta_Acl::getInstance()->getAccepdedRoles();
		array_push($acceptedRoles, Zetta_Acl::getInstance()->getMyGroup()); 
		
		$this->view->resources = $this->_modelResources->getResources($acceptedRoles);
		
	}
	
	public function privelegiesAction() {

		if ($this->getRequest()->isPost()) {
			
			$resource_name = $this->getParam('resource');
			
			foreach ($_POST as $role_name=>$access) {
			
				switch ($access) {
					case 'allow':
							$this->_modelRules->addRule($resource_name, $role_name, 'allow');
						break;
					case 'deny':
							$this->_modelRules->addRule($resource_name, $role_name, 'deny');
						break;
					case 'inherit':
							$this->_modelRules->removeRule($resource_name, $role_name);
						break;
				}
					
			}
			
			Zetta_Acl::resetInstance();
			
		}
		
		$this->view->accepted_roles = Zetta_Acl::getInstance()->getAccepdedRolesTree();
		$this->view->resource = $this->_modelResources->getResource($this->getParam('resource'));
		$this->view->my_role = Zetta_Acl::getInstance()->getMyGroup();
		
	}
	
	public function deleteAction() {

		if ($this->getRequest()->isPost() && $this->getParam('resource')) {
			$this->_modelResources->delete($this->_modelResources->getAdapter()->quoteInto('resource_name = ?', $this->getParam('resource')));
		}
		
	}

	public function addAction() {

		$form = new Zetta_Form(Zend_Registry::get('config')->Access->form->resource);
		
		$routes = Modules_Router_Model_Router::getInstance()->getRoutesTreeHash();
		$menuIdElement = $form->getElement('route_id');
		$menuIdElement->addMultiOptions($routes);
		
		if ($resource_id = $this->getParam('resource')) {
			
			$resource = $this->_modelResources->getResource($resource_id)->toArray();
			$resource['type'] = 'free';
			
			if (preg_match('/route_(\d*)/', $resource['resource_name'], $matches)) {
				$resource['route_id'] = $matches[1];
				$resource['type'] = 'router';
			}
			
			$this->view->resource = $resource;
			$form->setDefaults($resource);
			
			$form->getElement('type')->setAttrib('disabled', 'disabled');
			$form->getElement('resource_name')->setAttrib('disabled', 'disabled');

		}
		
		if (!sizeof($_POST) || !$form->isValid($_POST)) {
		    $this->view->form = $form;
		}
		else {

			if ($form->getValue('type') == 'router') {

				$arrayData = array(
					'resource_name'	=> $resource_name = 'route_' . $form->getValue('route_id'),
					'description'	=> 'Ограничение доступа к разделу "' . trim($routes[$form->getValue('route_id')], '- ') . '"',
				);

			}
			else {
				
				$arrayData = array(
					'resource_name'	=> $resource_name = $form->getValue('resource_name'),
					'description'	=> $form->getValue('description')
				);

			}
			
			if (!$arrayData['resource_name'] || !$arrayData['description']) {	// проверка, чтобы не добавлялись пустые привелегии
				return $this->renderScript('admin/addComplete.ajax.phtml');
			}

			if ($resource_id) {
				$this->_modelResources->update($arrayData, $this->_modelResources->getAdapter()->quoteInto('resource_name = ?', $resource_id));
			}
			else {
				
				$this->_modelResources->insert($arrayData);

				// дадим доступ администраторам по умолчанию
				$this->_modelRules->addRule($resource_name, 'admin', 'allow');

			}
			
			$this->renderScript('admin/addComplete.ajax.phtml');

		}
		
	}
	
	public function rolesAction() {
		$this->view->accepted_roles = Zetta_Acl::getInstance()->getAccepdedRolesTree();
	}
	
	public function sortrolesAction() {
		
		if (false == $this->getRequest()->isPost()) return ;
		
		foreach ($this->getParam('tree') as $row) {

			$where = $this->_modelRoles->getAdapter()->quoteInto('name = ?', $row['id']);

			$this->_modelRoles->update(array(
				'role_parent'	=> isset($row['parent_id']) ? $row['parent_id'] : Zetta_Acl::getInstance()->getMyGroup(),
				'sort'			=> $row['sort'],
			), $where);

		}
		
	}

	public function roleaddAction() {

		$form = new Zetta_Form(Zend_Registry::get('config')->Access->form->role);
		
		if ($role_id = $this->getParam('role_id')) {
			
			$data = $this->_modelRoles->getRole($role_id);
			$form->setDefaults($data->toArray());
			$this->view->role_name = $data->name;
			
			$form->getElement('name')->setAttrib('disabled', 'disabled');

		}
		
		if (!sizeof($_POST) || !$form->isValid($_POST)) {
		    $this->view->form = $form;
		}
		else {

			$arrayData = array(
				'name'			=> $form->getValue('name'),
				'description'	=> $form->getValue('description'),
			);
			
			if ($role_id) {
				$this->_modelRoles->update($arrayData, $this->_modelRoles->getAdapter()->quoteInto('name = ?', $role_id));
			}
			else {
				$arrayData['role_parent'] = Zetta_Acl::getInstance()->getMyGroup();
				$this->_modelRoles->insert($arrayData);
			}
			
			$this->renderScript('admin/addRoleComplete.ajax.phtml');

		}
		
	}

	public function roledeleteAction() {

		if ($this->getRequest()->isPost() && $role_id = $this->getParam('role_id')) {
			$this->_modelRoles->delete($this->_modelRoles->getAdapter()->quoteInto('name = ?', $role_id));
		}
		
	}
	
	public function rulesbyroleAction() {
		
		$role_id = $this->getParam('role_id');
		
		if (sizeof($_POST)) {
			
			$this->_modelRules->removeRoleRules($role_id);
			
			foreach ($_POST as $resource_name=>$access) {
										
				if ($resource_name == 'role_id') continue;
				
				switch ($access) {
					case 'allow':
							$this->_modelRules->addRule($resource_name, $role_id, 'allow');
						break;
					case 'deny':
							$this->_modelRules->addRule($resource_name, $role_id, 'deny');
						break;
				}
				
			}
			
			Zetta_Acl::resetInstance();
			
		}
		
		$this->view->role_name = $role_id;
		$this->view->resources = $this->_modelResources->getResources(Zetta_Acl::getInstance()->getMyGroup());
		
	}
	
}