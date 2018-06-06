<?php

class Modules_Editor_Plugin_Editor extends Zend_Controller_Plugin_Abstract
{
    protected $_view = null;


    public function __construct()
    {
        $this->_view = Zend_Registry::get('view');
    }

    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->_view->headLink()
            ->appendStylesheet($this->_view->libUrl('/Editor/public/js/imperavi/redactor.css'))
            ->appendStylesheet($this->_view->libUrl('/Editor/public/js/imperavi/plugins/alignment/alignment.css'))
            ->appendStylesheet($this->_view->libUrl('/Editor/public/js/imperavi/plugins/codemirror/lib/codemirror.css'))
            ->appendStylesheet($this->_view->libUrl('/Editor/public/js/imperavi/plugins/codemirror/lib/material.css'))
            
            ->appendStylesheet($this->_view->libUrl('/Editor/public/js/elFinder/css/elfinder.min.css'))
            ->appendStylesheet($this->_view->libUrl('/Editor/public/js/elFinder/css/theme.css'))

        ;

        $this->_view->headScript()
            ->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/redactor.js'))
            ->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/fontfamily/fontfamily.js'))
            ->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/fontsize/fontsize.js'))
            ->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/fontcolor/fontcolor.js'))
            ->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/undoredo/undoredo.js'))
            ->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/table/table.js'))
            ->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/clearformatting/clearformatting.js'))
            ->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/pin/pin.js'))
            ->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/filemanager/filemanager.js'))
            ->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/source/source.js'))
            ->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/codemirror/codemirror.js'))
            ->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/codemirror/lib/codemirror.js'))
            ->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/codemirror/lib/lang/htmlmixed.js'))
            ->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/codemirror/lib/lang/css.js'))
            ->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/codemirror/lib/lang/javascript.js'))
            ->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/codemirror/lib/lang/xml.js'))
            ->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/alignment/alignment.js'))
            ->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/video/video.js'))
            ->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/lang/ru.js'))

            ->appendFile($this->_view->libUrl('/Editor/public/js/elFinder/js/elfinder.full.js'))
            ->appendFile($this->_view->libUrl('/Editor/public/js/elFinder/js/i18n/elfinder.ru.js'))

            ->appendFile($this->_view->libUrl('/Editor/public/js/editor.js'))
        ;
    }
}
