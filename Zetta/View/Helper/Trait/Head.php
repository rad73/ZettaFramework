<?php

trait Zetta_View_Helper_Trait_Head
{
    public static $TEMP_DIR = '/public/.compiled';
    public static $DERIMITER = '__';

    /**
     * After output links clean storage
     *
     * @param  stdClass $item
     * @return string
     */
    public function toString($indent = null)
    {
        $return = parent::toString($indent);
        $this->getContainer()->exchangeArray(array());

        return $return;
    }


    /**
     * Check file is locall?
     *
     * @param string $path
     * @return bool
     */
    protected function _getLocalPathFile($path)
    {
        $parseUrl = parse_url(FILE_PATH . $path);

        return is_readable($parseUrl['path']) ? realpath($parseUrl['path']) : false;
    }

    /**
     * Get directory local file
     *
     * @param string $pathFile
     * @return string
     */
    protected function _getFileDirectory($pathFile)
    {
        if ($localPathFile = $this->_getLocalPathFile($pathFile)) {
            return dirname($localPathFile);
        }
    }

    /**
     * Get content file
     *
     * @param string $pathFile
     * @return string
     */
    protected function _getFileContent($pathFile)
    {
        $data = false;

        if ($localPathFile = $this->_getLocalPathFile($pathFile)) {
            $data = file_get_contents($localPathFile);
        } else {
            $url = preg_match('/https?:/', $pathFile) ? $pathFile : HTTP_HOST . $pathFile;

            $client = new Zend_Http_Client($url, array(
                'maxredirects' => 0,
                'timeout' => 5));

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
    protected function _getCompiledFileName($pathFile)
    {
        if ($localPathFile = $this->_getLocalPathFile($pathFile)) {
            $fileName = basename($localPathFile);
            $hash = filemtime($localPathFile);
        } else {
            $parseUrl = parse_url($pathFile);
            $fileName = basename($parseUrl['path']);
            $hash = crc32($pathFile);
        }

        return $fileName . self::$DERIMITER . $hash . '.css';
    }

    /**
     * Clean old compiled files
     *
     * @param string $fileName
     * @return string
     */
    protected function _clean($fileName)
    {
        $files = glob(FILE_PATH . self::$TEMP_DIR . DS . explode(self::$DERIMITER, $fileName)[0] . '*');

        if (sizeof($files)) {
            foreach ($files as $file) {
                unlink($file);
            }
        }
    }
}
