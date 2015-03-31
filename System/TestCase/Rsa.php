<?php

/**
 * Тестируем RSA кодирование - дектодирование
 *
 */
class System_TestCase_Rsa extends PHPUnit_Framework_TestCase {
	
	/**
	 * Раскодированная строка
	 *
	 * @var string
	 */
	private $_testString = 'Строка с переносами\r\nи прочими гадостями!!!\r\n\";№№\"%№;?;*asdfghxyz';
	
	public function testEncrypt() {
		
		$keys = System_Rsa_Keygen::Generate();
		$rsa = new System_Rsa_Rsalib($keys['module'], $keys['public'], $keys['private']);
		$output = $rsa->encrypt($this->_testString);
		$output = $rsa->decrypt($output);

		$this->assertEquals($this->_testString, $output);
		
	}
	
	public function testDecrypt() {

		$keys = System_Rsa_Keygen::Generate();
		$rsa = new System_Rsa_Rsalib($keys['module'], $keys['public'], $keys['private']);
		$output = $rsa->encrypt($this->_testString);
		$output = $rsa->decrypt($output);

		$this->assertEquals($this->_testString, $output);
		
	}
	
}