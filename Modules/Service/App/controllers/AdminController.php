<?php

class Modules_Service_AdminController extends Zend_Controller_Action
{
    public function init()
    {
        if (false == Zetta_Acl::getInstance()->isAllowed('admin_module_service')) {
            throw new Exception('Access Denied');
        }
        
        $this->_helper->getHelper('AjaxContext')
            ->addActionContext('index', 'html')
            ->addActionContext('disable', 'json')
            ->addActionContext('enable', 'json')
            ->addActionContext('update', 'json')
            ->addActionContext('restore', 'json')
            ->initContext();
    }
    
    public function indexAction()
    {
        $this->view->backups = glob(TEMP_PATH . DS . 'Backups/*', GLOB_ONLYDIR);
        $this->view->currentVersion = Modules_Service_Model_Update::getInstance()->currentVersion();
        $this->view->avalibleVersion = Modules_Service_Model_Update::getInstance()->avalibleVersion();
    }
    
    public function enableAction()
    {
        if ($this->getRequest()->isPost()) {
            Zend_Registry::get('SiteConfig')->disable_updates = 0;
        }
    }
    
    public function disableAction()
    {
        if ($this->getRequest()->isPost()) {
            Zend_Registry::get('SiteConfig')->disable_updates = 1;
        }
    }
    
    public function updateAction()
    {
        if ($this->getRequest()->isPost()) {
            Modules_Service_Model_Update::getInstance()->update();
        }
    }
    
    /**
     * Скачать архив с резервной копией
     *
     */
    public function downloadAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if ($this->hasParam('dir')) {
            $dir = $this->getParam('dir');
            $zip = new ZipArchive();
            
            $ret = $zip->open($zipFile = TEMP_PATH . DS . $dir . '.zip', ZipArchive::OVERWRITE);

            if ($ret !== true) {
                printf('Failed with code %d', $ret);
            } else {
                $options = array('add_path' => 'backup/', 'remove_all_path' => true);
                $zip->addGlob(TEMP_PATH . DS . 'Backups/' . $dir . '/*.*', GLOB_BRACE, $options);
                $zip->close();
                
                header('Content-Type: ' . System_Mime_Type::mime($zipFile));
                header('Content-Length: ' . filesize($zipFile));
                header("Content-Disposition: attachment; filename=\"".basename($zipFile)."\";");
                header("Content-Transfer-Encoding: binary");

                ob_end_flush();
                
                readfile($zipFile);
                unlink($zipFile);
    
                exit;
            }
        }
    }
    
    /**
     * Восстановить из резервной копии
     *
     */
    public function restoreAction()
    {
        if ($this->getRequest()->isPost() && $this->hasParam('dir')) {
            Modules_Service_Model_Backup::getInstance()->restore($this->getParam('dir'));
        }
    }
}
