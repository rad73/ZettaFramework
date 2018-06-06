<?php

class Zetta_View_Helper_View extends Zend_View_Helper_Abstract
{

    /**
     * Получаем ссылку на Zend_View
     *
     * @return Zend_View
     */
    public function view()
    {
        return Zend_Registry::get('view');
    }
}
