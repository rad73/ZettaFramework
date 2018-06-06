<?php

require_once 'LibController.php';

class Modules_Application_ThumbController extends Modules_Application_LibController
{

    /**
     * Доступ к файлу есть всегда
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Поиск файла в системе
     *
     * @return string	Путь к найденому файлу
     */
    protected function _findFile()
    {
        $requestUri = parse_url($this->getRequest()->getRequestUri());
        $baseUri = $this->getRequest()->getBaseUrl();
        $findFilePath = FILE_PATH . str_ireplace($baseUri, '', urldecode($requestUri['path']));

        preg_match('/(.*)\/thumbs\/(.*\/)?(.*)_(\d+)x(\d+)(_w)?(@.*x)?(\.[a-z]+)(\.[a-z]+)?$/', $findFilePath, $matches);
        /*
        $matches = array(10) {
              [0]=> string(113) "/home/edostavka/www/img.e-dostavka.by/UserFiles/images/catalog/Goods/thumbs/4810/4810282009778_190x190@2x.png.jpg"
              [1]=> string(68) "/home/edostavka/www/img.e-dostavka.by/UserFiles/images/catalog/Goods"	// папка где лежит оригинал
              [2]=> string(5) "4810/"	// префикс папки с уменьшенными копиями
              [3]=> string(13) "4810282009778"	// название изображения
              [4]=> string(3) "190"				// желаемая ширина
              [5]=> string(3) "190"				// желаемая высота
              [6]=> string(0) "_w"				// использовать ли watermark
              [7]=> string(3) "@2x"				// фактор увеличения
              [8]=> string(4) ".png"			// расширение оригинала
              [9]=> string(4) ".jpg"			// расширение уменьшенной копии
            }
        */

        if (sizeof($matches) >= 9) {
            $prefixFolder = $matches[1];

            $file = $matches[1] . DS . $matches[3] . $matches[8];

            if (false == is_file($file)) {
                throw new Exception('File ' . $file . ' not exists');
            }

            $thumbDir = dirname($findFilePath);
            if (false == is_dir($thumbDir)) {
                mkdir($thumbDir, 0777, true);
            }

            $width = intval($matches[4]);
            $height = intval($matches[5]);

            if ($scale = preg_replace('/[^0-9.]/', '', $matches[7])) {
                $width *= doubleval($scale);
                $height *= doubleval($scale);
            }

            return ($matches[6] == '_w')
                ? System_Functions::createThumbWatermark($file, $findFilePath, $width, $height)
                : System_Functions::createThumb($file, $findFilePath, $width, $height);
        }

        throw new Exception('Incorrect file format ' . $findFilePath);
    }
}
