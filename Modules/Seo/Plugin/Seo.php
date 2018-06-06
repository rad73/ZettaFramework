<?php

/**
 * Плагин для замены стандартных title, keywords, description на значения из модуля
 *
 */
class Modules_Seo_Plugin_Seo extends Zend_Controller_Plugin_Abstract
{
    protected $_view;
    protected $_data;
    
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $this->_view = Zend_Registry::get('view');
        
        $currentUrl = $this->_view->currentUrl('currentUrl');
        $model = new Modules_Seo_Model_Seo();
        
        $this->_data = $model->findByUrl($currentUrl);
    }
    
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (sizeof($this->_data)) {
            if ($this->_data->title) {
                $this->_view->headTitle()->exchangeArray(array());
                $this->_view->headTitle()->headTitle($this->_data->title);
            }
            
            if ($this->_data->description) {
                $this->_view->headMeta()->setName('description', $this->_data->description);
            }
            
            if ($this->_data->keywords) {
                $this->_view->headMeta()->setName('keywords', $this->_data->keywords);
            }
        }
    }
}
