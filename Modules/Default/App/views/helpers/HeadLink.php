<?php

use Leafo\ScssPhp\Compiler;

class Zetta_View_Helper_HeadLink extends Zend_View_Helper_HeadLink {

	const TEMP_DIR = '/public/.compiled';

	const DERIMITER = '__';


	public function itemToString(stdClass $item) {

		if (($isLess = $this->_isLess($item->href)) || ($isScss = $this->_isScss($item->href))) {

			$compiledFileName = $this->_getCompiledFileName($item->href);
			$nonCompiledFileName = $item->href;
			$nonCompiledDirName = $this->_getFileDirectory($item->href);
			$item->href = self::TEMP_DIR . DS . $compiledFileName;

			if (!is_readable($savePath = FILE_PATH . $item->href)) {

				switch(true) {
					case $isLess:
							$compiler = new lessc();
							$compiler->setImportDir($nonCompiledDirName);
						break;

					case $isScss:
							$compiler = new Compiler();
							$compiler->setLineNumberStyle(Compiler::LINE_COMMENTS);
							$compiler->setImportPaths($nonCompiledDirName);
						break;
				}

				$compiled = $compiler->compile($this->_getFileContent($nonCompiledFileName));
				$this->_clean($compiledFileName);
				file_put_contents($savePath, $compiled);

			}

		}

		return parent::itemToString($item);

	}

	/**
	 * Check file is less?
	 * 
	 * @param string $path
	 * @return bool
	 */
	protected function _isLess($path) {

		if (stristr($path, '.less')) {
			return true;
		}

	}

	/**
	 * Check file is scss?
	 * 
	 * @param string $path
	 * @return bool
	 */
	protected function _isScss($path) {

		if (stristr($path, '.scss') || stristr($path, '.sass')) {
			return true;
		}

	}

	/**
	 * Check file is locall?
	 * 
	 * @param string $path
	 * @return bool
	 */
	protected function _isFileLocal($path) {
		return is_readable($path);
	}

	/**
	 * Get directory local file
	 * 
	 * @param string $pathFile
	 * @return string
	 */
	protected function _getFileDirectory($pathFile) {

		if ($this->_isFileLocal(FILE_PATH . $pathFile)) {
			return dirname(FILE_PATH . $pathFile);
		}

	}

	/**
	 * Get content file
	 * 
	 * @param string $pathFile
	 * @return string
	 */
	protected function _getFileContent($pathFile) {

		if ($this->_isFileLocal(FILE_PATH . $pathFile)) {
			$data = file_get_contents(FILE_PATH . $pathFile);
		}
		else {

			$url = preg_match('/https?:/', $pathFile) ? $pathFile : HTTP_HOST . $pathFile;

			$client = new Zend_Http_Client($url, array(
				'maxredirects' => 0,
				'timeout'      => 5));

			$response = $client->request();

			if (200 == $response->getStatus()) {
				$data = $response->getBody();
			}

		}

		return $data;

	}

	/**
	 * Generate new compiled file name
	 * 
	 * @param string $pathFile
	 * @return string
	 */
	protected function _getCompiledFileName($pathFile) {


		if ($this->_isFileLocal(FILE_PATH . $pathFile)) {

			$fileName = basename($pathFile);
			$hash = filemtime(FILE_PATH . $pathFile);

		}
		else {
			
			$parseUrl = parse_url($pathFile);
			$fileName = basename($parseUrl['path']);
			$hash = crc32($pathFile);

		}

		return $fileName . self::DERIMITER . $hash . '.css';

	}

	/**
	 * Clean old compiled files
	 * 
	 * @param string $fileName
	 * @return string
	 */
	protected function _clean($fileName) {

		$files = glob(FILE_PATH . self::TEMP_DIR . DS . explode(self::DERIMITER, $fileName)[0] . '*');

		if (sizeof($files)) {
			
			foreach ($files as $file) {
				unlink($file);
			}

		}

	}

}