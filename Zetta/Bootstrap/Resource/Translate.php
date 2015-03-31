<?php

/**
 * Настраиваем Translate
 *
 */
class Zetta_Bootstrap_Resource_Translate extends Zend_Application_Resource_Translate {

	public function init() {
		
		parent::init();
		
		$modules = Zend_Registry::get('modules');

		foreach ($modules as &$path) {
			
			if (is_dir($path . DS . 'Locales')) {
				
				$files = glob($path . DS . 'Locales' . DS . '*.*');
				
				if (sizeof($files)) {

					foreach ($files as $file) {
						
						//$name = basename($file);
						//list($locale, $extension) = explode('.', $name);

						$this->_translate->addTranslation(
						    array(
						        'content' => $file,
						        'locale'  => 'auto'
						    )
						);
						
					}
				
				}
				
			}
			
		}
		
	}

}