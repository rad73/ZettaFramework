<?php

class Zetta_View_Helper_CurrentUrl extends Zend_View_Helper_Abstract
{
    protected $_request;

    
    public function currentUrl()
    {
        $this->_request = Zend_Controller_Front::getInstance()->getRequest();
        
        return $this->_normilizeUrl($this->_request->getBaseUrl() . $this->_request->getPathInfo());
    }

    protected function _normilizeUrl($url)
    {
        $url = ('/' == substr($url, -1)) ? $url : ($url . '/');

        return str_ireplace($this->view->baseUrl(), '', $url);
    }
}
