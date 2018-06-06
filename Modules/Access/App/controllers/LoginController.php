<?php

class Modules_Access_LoginController extends Zend_Controller_Action
{
    protected $sessionAuth;

    protected $_user;

    public function init()
    {
        $this->_user = Modules_Access_Framework_User::getInstance();
        
        if (false != $this->_user->getUserName() && $this->getRequest()->getActionName() != 'logout') {
            $this->_redirect('/');
        }
        
        $this->_helper->layout->setLayout('login');
    }

    public function indexAction()
    {
        if ($this->_user->getUserName() == false) {
            $this->view->showError = $this->getRequest()->isPost();
            $this->view->username = $this->_getParam('username');
            
            $keys = Modules_Access_Framework_Auth_Plugin_RequestRsa::getKeys();
            $this->view->hash = $keys->public . '~' . $keys->module;
        }
    }

    public function logoutAction()
    {
        if ($this->getRequest()->isPost()) {
            Zend_Auth::getInstance()->logOut();
            $this->_redirect(Zend_Registry::get('http_referer'), array('prependBase' => false));
        } else {
            $this->_redirect('/');
        }
    }
}
