<?php

class Modules_Accessusers_IndexController extends Zend_Controller_Action {

	/**
	 * Модель пользователей
	 *
	 * @var Modules_Access_Model_Users
	 */
	protected $_model;
	
	protected $_configFormRegistration;
	protected $_configFormEnter;
	

	public function init() {
		
		$this->_model = new Modules_Access_Model_Users();
		
		$this->_configFormRegistration = Zend_Registry::get('config')->Accessusers->form->registration;
		$this->_configFormEnter = Zend_Registry::get('config')->Accessusers->form->enter;
		
	}

	public function registrationAction() {

		$form = new Zetta_Form($this->_configFormRegistration);
		
		if ($this->getRequest()->isPost() && $this->getParam('registration') && $form->isValid($_POST)) {

			$arrayData = $this->_makeInsertRegistration($form);
			
			$this->_model->insert($arrayData);
			
			$this->renderScript('index/registrationComplete.phtml');
			$this->_sendRegLetter();

		}
		else {

			$saltElement = $form->getElement('salt');
			$saltElement->setValue(Modules_Access_Model_Users::GenerateSalt());
			
		    $this->view->form = $form;

		}

	}
	
	public function enterAction() {

		$form = new Zetta_Form($this->_configFormEnter);
		
		if (
			$this->getRequest()->isPost() 
			&& $this->getParam('enter') 
			&& $form->isValid($_POST)
		) {
		
			if (!Zend_Auth::getInstance()->getIdentity()) {
				$this->view->form = $form;
				$this->view->error_enter = 1;
			}
			
		}
		else {

		    $this->view->form = $form;

		}
		
	}
	
	public function forgotAction() {
		
		if ($this->getParam('hash') && $this->getParam('newpassword')) {
			$this->forward('restorepassword');
		}
		
		if ($this->getRequest()->isPost() && $this->getParam('forgot_email')) {

			$user = $this->_model->fetchRow($this->_model->select()
				->where('email = ?', $this->getParam('forgot_email'))
			);
			
			if ($user) {
				
				$this->renderScript('index/forgot_complete.phtml');
				$this->_sendForgotLetter($user);
				
			}
			else {
				$this->view->error = 1;
				$this->view->email = $this->getParam('forgot_email');
			}
			
		}
		
	}
	
	public function restorepasswordAction () {
		
		if ($this->getParam('hash') && $this->getParam('newpassword')) {
			
			$user = $this->_model->fetchRow($this->_model->select()
				->where('salt = ?', $this->getParam('hash'))
			);
			
			if ($user) {
				
				$this->_model->update(array(
					'password'	=> md5(Zend_Registry::get('config')->Db->staticSalt . md5($this->getParam('newpassword')) . $user->salt),
				), $this->_model->getAdapter()->quoteInto('username = ?', $user->username));
				
			}
			
		}
		
	}
	
	protected function _sendRegLetter() {
		
		$this->view->request = $this->getAllParams();
		$body = $this->view->render('index/mail_registration.phtml');
		
		$mail = new Zend_Mail('utf-8');
		$mail->addTo($this->getParam('email'));
		$mail->setFrom(Zend_Registry::get('SiteConfig')->mail_from);
		$mail->setSubject('Вы успешно зарегистрировались на сайте ' . HTTP_HOST);
		$mail->setBodyHtml($body);
		$mail->send();
		
	}
	
	protected function _sendForgotLetter($user) {
		
		$this->view->newPassword = substr(md5(rand()), 0, 7);
		$this->view->link = HTTP_HOST . $this->view->url(array('module' => 'accessusers', 'controller' => 'index', 'action' => 'forgot', 'hash' => $user->salt, 'newpassword' => $this->view->newPassword));
		$body = $this->view->render('index/mail_forgot.phtml');
		
		$mail = new Zend_Mail('utf-8');
		$mail->addTo($user->email);
		$mail->setFrom(Zend_Registry::get('SiteConfig')->mail_from);
		$mail->setSubject('Восстановление пароля на сайте ' . HTTP_HOST);
		$mail->setBodyHtml($body);
		$mail->send();
		
	}

	/**
	 * Подготовка массива для регистрации
	 *
	 * @param Zend_Form $form
	 * @return array
	 */
	protected function _makeInsertRegistration($form) {
		
		return array(
			'salt'		=> $form->getValue('salt'),
			'active'	=> 1,
			'email'		=> $form->getValue('email'),
			'name'		=> $form->getValue('name'),
			'sername'	=> $form->getValue('sername'),
			'role_name'	=> 'user',
			'password'	=> md5(Zend_Registry::get('config')->Db->staticSalt . md5($form->getValue('password')) . $form->getValue('salt')),
			'username'	=> $form->getValue('username'),
		);
		
	}
	
}