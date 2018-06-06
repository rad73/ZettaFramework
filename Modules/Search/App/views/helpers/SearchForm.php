<?php

class Zetta_View_Helper_SearchForm extends Zend_View_Helper_Action
{
    public function searchForm()
    {
        $view = $this->cloneView();

        $view
            ->addBasePath(MODULES_PATH . DS . 'Search/App/views')
            ->addBasePath(HEAP_PATH . DS . 'Search/App/views');
            
        return $view->render('index/widget.phtml');
    }
}
