<?php

/**
 * Вывод меню
 *
 */
class Zetta_View_Helper_Menu extends Zend_View_Helper_Abstract
{
    protected $_request;

    public function menu($menu_id)
    {
        $_modelMenu = new Modules_Menu_Model_Menu();
        
        $this->view
            ->addBasePath(HEAP_PATH . DS . 'Menu/App/views')
            ->addBasePath(MODULES_PATH . DS . 'Menu/App/views');

        $this->view->menu_id = $menu_id;
        $this->view->tree = $_modelMenu->getMenuTree($menu_id);
        
        try {
            $return = $this->view->render('menu_' . $menu_id . '/index.phtml');
        } catch (Exception $e) {
            $return = $this->view->render('menu/index.phtml');
        }
        
        if (Zetta_Acl::getInstance()->isAllowed('admin_module_menu')) {
            try {
                $this->view->content = $return;
                $return = $this->view->render('menu_' . $menu_id . '/adminWrapper.phtml');
            } catch (Exception $e) {
                $return = $this->view->render('menu/adminWrapper.phtml');
            }
        }
        
        return $return;
    }
}
