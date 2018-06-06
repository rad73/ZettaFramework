<?php

class Zetta_View_Helper_RenderAdmin extends Zend_View_Helper_Abstract
{
    public function renderAdmin($name, $placeholderName = 'content', $model = null)
    {
        $result = $this->view->placeholder($placeholderName)->toString();
        
        if ($this->_access()) {
            if (sizeof($model)) {
                foreach ($model as $k => $v) {
                    $this->view->$k = $v;
                }
            }
            
            $this->view->content = $result;
            $result = $this->view->render($name);
        }
        
        Zend_View_Helper_Placeholder_Registry::getRegistry()->deleteContainer($placeholderName);
        
        return $result;
    }
    
    protected function _access()
    {
        return Zetta_Acl::getInstance()->isAllowed('admin_module_blocks');
    }
}
