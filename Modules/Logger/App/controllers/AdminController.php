<?php

class Modules_Logger_AdminController extends Zend_Controller_Action
{
    protected $_logFilePath;
    
    public function init()
    {
        if (false == Zetta_Acl::getInstance()->isAllowed('admin_module_logger')) {
            throw new Exception('Access Denied');
        }
        
        $this->_helper->getHelper('AjaxContext')
            ->addActionContext('index', 'html')
            ->addActionContext('clear', 'html')
            ->initContext();
        
        $loggerConfig = new Zend_Config_Ini(SYSTEM_PATH . '/Configs/_log.ini', ZETTA_MODE);
        $this->_logFilePath = $loggerConfig->resources->log->stream->writerParams->stream;
    }
    
    public function indexAction()
    {
        $logFile = $this->_logFilePath;
        
        if (is_file($logFile)) {
            $logData = '<log>' . file_get_contents($logFile) . '</log>';
            $this->view->log = simplexml_load_string($logData)->xpath('//logEntry');
            
            $this->view->download = false;
            if (filesize($logFile) > 1024 * 200) {	// если файл больше 100Kb предлагаем его скачать
                $this->view->download = true;
                $this->view->size = round(filesize($logFile) / 1024);
            }
        }
    }
    
    public function clearAction()
    {
        if ($this->getRequest()->isPost()) {
            unlink($this->_logFilePath);
        }
        
        $this->forward('index');
    }
    
    public function downloadAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        echo '<log>' . file_get_contents($this->_logFilePath) . '</log>';
        $this->getResponse()
            ->setHeader('Content-Disposition', 'attachment; filename=log.xml')
            ->setHeader('Content-type', 'text/xml');
    }
}
