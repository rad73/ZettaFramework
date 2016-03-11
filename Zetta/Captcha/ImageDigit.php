<?php

class Zetta_Captcha_ImageDigit extends Zend_Captcha_Image {

    protected function _generateWord() {

		$wordLen = $this->getWordLen();

		$from = pow(10, $wordLen - 1);
		$to = pow(10, $wordLen) - 1;

		return strval(Zend_Crypt_Math::randInteger($from, $to, true));

    }

}