<?php

class Modules_Accessusers_AdminController extends Zend_Controller_Action {

	protected $_modelUsers;
	protected $_modelRoles;

	protected $_acceptedRoles;


	public function init() {

		if (false == Zetta_Acl::getInstance()->isAllowed('admin_module_accessusers')) {
			throw new Exception('Access Denied');
		}

		$this->_modelUsers = new Modules_Access_Model_Users();
		$this->_modelRoles = new Modules_Access_Model_Roles();

		$this->_acceptedRoles = Zetta_Acl::getInstance()->getAccepdedRoles();

		$this->_helper->getHelper('AjaxContext')
        	->addActionContext('index', 'html')
        	->addActionContext('users', 'html')
        	->addActionContext('add', 'html')
        	->addActionContext('delete', 'json')
            ->initContext();

	}

	public function indexAction() {
		$this->view->accepted_roles = Zetta_Acl::getInstance()->getAccepdedRolesTree();
	}

	public function usersAction() {

		if ($role_id = $this->getParam('role_id')) {
			$this->view->users = $this->_modelUsers->getUsersInRole($role_id);
			$this->view->role = $this->_modelRoles->getRole($role_id);
		}
		else {
			$this->view->users = $this->_modelUsers->getUsers();
		}

	}

	public function addAction() {

		if ($roleInfo = $this->hasParam('role_id')) {
			$roleInfo = $this->view->role = $this->_modelRoles->getRole($this->getParam('role_id'));
		}

		$form = new Zetta_Form(Zend_Registry::get('config')->Accessusers->form->admin_adduser);

		$rolesElement = $form->getElement('role_name');
		$rolesElement->addMultiOptions(Zetta_Acl::getInstance()->getAccepdedRolesHash());
		if (is_object($roleInfo)) {
			$rolesElement->setValue($roleInfo->name);
		}

		if ($user_id = $this->getParam('login')) {

			$this->view->user_id = $user_id;
			$editUserData = $this->_modelUsers->getUser($user_id);
			$form->setDefaults($editUserData->toArray());

			$form->getElement('username')->setAttrib('disabled', 'disabled');
			$form->getElement('password')->setRequired(false);
			$form->getElement('re_password')->setRequired(false);


			$myUser = Zend_Auth::getInstance()->getIdentity();
			if ($myUser->username == $user_id) {
				$form->removeElement('role_name');
			}

		}
		else {
			$saltElement = $form->getElement('salt');
			$saltElement->setValue(Modules_Access_Model_Users::GenerateSalt());
		}

		if (!sizeof($_POST) || !$form->isValid($_POST)) {
		    $this->view->form = $form;
		}
		else {

			$arrayData = array(
				'salt'		=> $form->getValue('salt'),
				'active'		=> (int)$form->getValue('active'),
				'email'		=> $form->getValue('email'),
				'name'		=> $form->getValue('name'),
				'sername'		=> $form->getValue('sername'),
			);

			if ($form->getValue('role_name')) {
				$arrayData['role_name'] = $form->getValue('role_name');
			}

			if ($form->getValue('password')) {

				$arrayData['password'] = md5(Zend_Registry::get('config')->db->staticSalt . md5($form->getValue('password')) . $form->getValue('salt'));

				if ($this->getParam('login') == Modules_Access_Framework_User::getInstance()->getUserName()) {
					$stdObject = Zend_Auth::getInstance()->getStorage()->read();
					$stdObject->password = $arrayData['password'];
					Zend_Auth::getInstance()->getStorage()->write($stdObject);
				}

			}

			if ($user_id) {
				$this->_modelUsers->update($arrayData, $this->_modelUsers->getAdapter()->quoteInto('username = ?', $user_id));
			}
			else {
				$arrayData['username'] = $form->getValue('username');

				$this->_modelUsers->insert($arrayData);
			}

			$this->renderScript('admin/addComplete.ajax.phtml');

		}

	}

	public function deleteAction() {

		if ($this->getRequest()->isPost()) {
			$this->_modelUsers->deleteUser($this->getParam('login'));
			$this->view->clearVars();
		}

	}

}