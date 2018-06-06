<?php

/**
 * Плагин для размещения иконки в панели виджетов
 *
 */
class Modules_Accessusers_Plugin_Widget extends Zend_Controller_Plugin_Abstract
{
    protected $_view = null;
    

    public function __construct()
    {
        $this->_view = Zend_Registry::get('view');
    }
    
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        if (Zetta_Acl::getInstance()->isAllowed('admin')) {
            $this->_view->renderWidget(MODULES_PATH . DS . 'Accessusers/App/views', 'admin/widget.phtml', array(
                'user' => Zend_Auth::getInstance()->getIdentity()
            ));
            
            $this->_view->headLink()
                ->appendStylesheet($this->_view->libUrl('/Accessusers/public/css/admin.css'));
        }
    }
}
