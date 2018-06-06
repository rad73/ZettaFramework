<?php

class Modules_Service_UpdateController extends Zend_Controller_Action
{
    public function init()
    {
        if ($this->getParam('secret_key') != Zend_Registry::get('config')->Db->staticSalt) {
            throw new Exception('Access Deny BackupController via http');
        }

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }
    
    public function indexAction()
    {
        if (Zend_Registry::get('SiteConfig')->disable_updates == 1) {
            return;
        }
        Modules_Service_Model_Update::getInstance()->update(false, $this->getParam('skip_zetta'));
    }
}
