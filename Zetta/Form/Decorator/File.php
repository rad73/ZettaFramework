<?php

class Zetta_Form_Decorator_File extends Zend_Form_Decorator_File {
    
    /**
     * Render a form file
     *
     * @param  string $content
     * @return string
     */
    public function render($content) {
		
		$return = parent::render($content);
        $element = $this->getElement();
		$value = $element->getValue();
		$files_list = '';
		
		if ($value) {
			$arrayFiles = ($array = json_decode($value)) ? $array : array($value);
			
			if (sizeof($arrayFiles)) {
				
				foreach ($arrayFiles as $file) {
					$files_list .= '<br/><a href="' . $file . '" target="_blank" class="no_ajax">' . $file . '</a>';
				}
				
			}

		}
		
		return $return . $files_list;

    }

}
