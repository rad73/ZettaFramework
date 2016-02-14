<?php


class Zetta_Form_Element_ZettaFile extends Zend_Form_Element_File {

	/**
     * Validate upload. If file in not required valid = true all time
     *
     * @param  string $value   File, can be optional, give null to validate all files
     * @param  mixed  $context
     * @return bool
     */
    public function isValid($value, $context = null) {

		if (!$this->isRequired()) {
			 $this->_validated = true;
            return true;
		}

		return parent::isValid($value, $context);

    }

}