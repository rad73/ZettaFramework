<?php

class Zetta_Form_Decorator_Errors extends Zend_Form_Decorator_Errors {
    
	public function getElement()
    {
		$element = parent::getElement();
		
		// Get error messages
        if ($element instanceof Zend_Form
            && null !== $element->getElementsBelongTo()
        ) {
            $errors = $element->getMessages(null, true);
        } else {
            $errors = $element->getMessages();
        }

        if (!empty($errors)) {
			$decoratorRow = $element->getDecorator('row');
			$currentClasses = $decoratorRow->getOption('class');
			$decoratorRow->setOption('class', $currentClasses . ' form_row__error');
        }
		
        return $element;
    }


}
