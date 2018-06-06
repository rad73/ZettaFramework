<?php

class Modules_Application_Bootstrap extends Zetta_BootstrapModules
{
    public function _bootstrap()
    {
        parent::_bootstrap();

        Zend_Controller_Front::getInstance()
            ->registerPlugin(new Zend_Controller_Plugin_ActionStack())
            ->registerPlugin(new Modules_Application_Plugin_Referer())
            ->registerPlugin(new Modules_Application_Plugin_CleanBaseUrl(), -999999)
            ->registerPlugin(new Modules_Application_Plugin_Csrf(), -1000000);	// защита от csrf атак должна отрабатываться самой первой
    }
}
