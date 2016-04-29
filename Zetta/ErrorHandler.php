<?php

/**
 * ErrorHandler
 *
 */
class Zetta_ErrorHandler  {

	public static $DISABLE = false;

	public function error() {

		if (false == self::$DISABLE) {

			$response = Zend_Controller_Front::getInstance()->getResponse();
			if ($response && $response->getBody() && sizeof($response->getException())) {
				// исключение уже обработано
				return;
			}

			if ($response && !$response->getBody() && sizeof($response->getException())) {

				$frontControllerException = $response->getException();
				$frontControllerException = $frontControllerException[0];

				$error['message'] = $frontControllerException->getMessage();
				$error['file'] = $frontControllerException->getFile();
				$error['line'] = $frontControllerException->getLine();

			}
			else {
				$error = error_get_last();
			}

			if ($error) {

				if (Zend_Registry::isRegistered('Logger')) {

					Zend_Registry::get('Logger')->log(htmlspecialchars($error['message']), Zend_Log::CRIT, array(
						'file'	=>  $error['file'],
						'line'	=> $error['line']
					));

				}

				header('HTTP/1.1 500 Internal Server Error');

				if (ini_get('display_errors')) {
					ob_end_clean();
					echo 'ZettaCMS: ' . htmlspecialchars($error['message']) . ' in ' . $error['file'] . ':' . $error['line'];
				}
				else {
					ob_end_clean();
					echo 'На сайте проводятся профилактические работы. Ожидайте.';
					exit;
				}

			}

		}

	}

}