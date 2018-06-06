<?php

/**
 * Вывод редактируемого блока
 *
 */
class Zetta_View_Helper_Block extends Zend_View_Helper_Abstract
{
    protected $_blockModel;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = clone $view;

        $this->view
            ->setBasePath(HEAP_PATH . DS . 'Blocks/App/views')
            ->addBasePath(MODULES_PATH . DS . 'Blocks/App/views');

        $this->_blockModel = new Modules_Blocks_Model_Blocks();

        if ($this->isAdmin()) {
            $route_id_current = Zend_Registry::get('RouteCurrentId');

            $this->view->headScript()
                ->appendFile($this->view->libUrl('/Blocks/public/js/admin.js'))
                ->prependScript('
					var _urlBlockSave = "' . $this->view->url(array('module' => 'blocks', 'controller' => 'admin', 'action' => 'save'), 'mvc', true) . '",
						_urlBlockInfo = "' . $this->view->url(array('module' => 'blocks', 'controller' => 'admin', 'action' => 'blockinfo'), 'mvc', true) . '",
						_urlBlockDelete = "' . $this->view->url(array('module' => 'blocks', 'controller' => 'admin', 'action' => 'blockdelete'), 'mvc', true) . '",
						_currentRouteId = ' . intval($route_id_current) . ';
				');
        }
    }

    public function block($blockName, $blockType = 'html', $defaultValue = false, $inherit = true, $vsprintfParams = false)
    {
        $block = $this->_blockModel->getBlock($blockName, $inherit);
        $this->view->block = $block;
        $this->view->block_name = $blockName;
        $this->view->block_type = $blockType;
        $this->view->content = $block ? $block->content : $defaultValue;
        $this->view->inherit = $inherit;

        // @todo slow perfomance
        //try {
        //	$return = $this->view->render('block_' . $blockName . '/index.phtml');
        //}
        //catch (Exception $e) {
        //	$return = $this->view->render('block/index.phtml');
        //}

        $return = $this->view->render('block/index.phtml');

        if ($this->isAdmin()) {
            $this->view->content = $return;
            $return = $this->view->render('block/adminWrapper.phtml');
        }

        return is_array($vsprintfParams) && !$this->isAdmin() ? vsprintf($return, $vsprintfParams) : $return;
    }


    protected function isAdmin()
    {
        return Zetta_Acl::getInstance()->isAllowed('admin_module_blocks');
    }
}
