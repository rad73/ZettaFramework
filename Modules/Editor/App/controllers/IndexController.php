<?php


class Modules_Editor_IndexController extends Zend_Controller_Action {
	
	public function init() {
		
		if (false == Zetta_Acl::getInstance()->isAllowed('admin_module_filemanager')) {
			throw new Exception('Access Denied');
		}
		
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
	}
	
	public function elfinderconnectorAction() {
		
		require_once 'Modules/Editor/public/js/elFinder/php/connector.minimal.php';
		
	}
	
    public function imagesAction() {

    	$imagePath = USER_FILES_PATH;
    	$folders = System_Functions::getFolderFilesRecursive($imagePath . DS . 'images', array('jpg', 'png', 'gif'));
    	$result = array();

    	foreach ($folders as $filename=>$folder) {
    		
    		
    		$fileRelative = explode($imagePath, $filename);
    		$fileRelative = $fileRelative[1];
    		
    		$fileHttpPath = $this->view->baseUrl() . DS . 'UserFiles' . $fileRelative;

    		
    		if (strstr($fileHttpPath, 'thumbs')) continue;

    		$result[] = array(
    			'thumb'	=> System_Functions::getThumbUrl($fileHttpPath, array(100, 75)),
    			'image'	=> $fileHttpPath,
    			'title'	=> basename($fileRelative),
    			'folder'	=> dirname($fileRelative),
    		);

		}
		
		echo json_encode($result);
		
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
    	
    }
    
    public function imageuploadAction() {
		
		$dir = USER_FILES_PATH . DS . 'images' . DS;
		
		$_FILES['file']['type'] = strtolower($_FILES['file']['type']);
		
		if (in_array($_FILES['file']['type'], array('image/png', 'image/png', 'image/jpg', 'image/gif', 'image/jpeg', 'image/pjpeg'))) {
		    
			$name = explode('.', $_FILES['file']['name']);
			$ext = '.' . $name[sizeof($name) - 1];
			$fileName = str_replace($ext, '', $_FILES['file']['name']);
			
		    $filename = System_String::translit($fileName) . date('_Hms') . $ext;
		    $file = $dir . $filename;

		    move_uploaded_file($_FILES['file']['tmp_name'], $file);
		    chmod($file, 0777);

			echo json_encode(array(
				'filelink' => $this->view->baseUrl() . DS . 'UserFiles/images' . DS . $filename
			));

		}
		
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

    }
    
    public function fileuploadAction() {
    	
    	$dir = USER_FILES_PATH . DS . 'files' . DS;
		
		$name = explode('.', $_FILES['file']['name']);
		$ext = '.' . $name[sizeof($name) - 1];
		$fileName = str_replace($ext, '', $_FILES['file']['name']);
		
	    $filename = System_String::translit($fileName) . date('_Hms') . $ext;
	    $file = $dir . $filename;

	    move_uploaded_file($_FILES['file']['tmp_name'], $file);
		chmod($file, 0777);

		echo json_encode(array(
			'filelink' => $this->view->baseUrl() . DS . 'UserFiles/files' . DS . $filename,
			'filename' => basename($file)
		));

		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
    	
    }

}