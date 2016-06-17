<?php

class Modules_Search_CronController extends Zend_Controller_Action {

	protected $_indexHandle;

	protected $_indexedUrl = array();

	public function init() {

		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		ini_set('max_execution_time', 0);

		$indexPath = TEMP_PATH . '/Search';
		$files = glob($indexPath . '/*.*', GLOB_NOSORT);
		// чистим старй индекс
		foreach ($files as $file) {
			unlink($file);
		}

		$this->_indexHandle = Zend_Search_Lucene::create($indexPath);

	}

	public function indexateAction() {

		Zetta_ErrorHandler::$DISABLE = true;
		$this->_indexate($this->view->baseUrl());
		$this->_indexHandle->optimize();

	}

	protected function _indexate($url) {

		if (!stristr($url, 'http://')) {
			$url = HTTP_HOST . $url;
		}

		$url = substr($url, -1) == '/' ? substr($url, 0, -1) : $url;

		if (!in_array($url, $this->_indexedUrl)) {

			if (stristr($url, HTTP_HOST)) {

				array_push($this->_indexedUrl, $url);

				$html = file_get_contents($url);

				libxml_use_internal_errors(true);
				$doc = Zend_Search_Lucene_Document_Html::loadHTML($html);
				libxml_use_internal_errors(false);

				if (preg_match('/<\!--index-->(.*)<\!--\/index-->/isu', $html, $matches)) {
					$html = $matches[1];
				}

				$html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
				$html = strip_tags($html);


				$doc->addField(Zend_Search_Lucene_Field::Text('content', $html, 'utf-8'));

				$doc->addField(Zend_Search_Lucene_Field::UnIndexed('body', '', 'utf-8'));
				$doc->addField(Zend_Search_Lucene_Field::Text('url', $url, 'utf-8'));

				$this->_indexHandle->addDocument($doc);

				Zend_Registry::get('Logger')->info('Search index is created: ' . $url, Zend_Log::INFO);

				foreach ($doc->getLinks() as $link) {

					$temp = explode('.', $link);
					$ext = end($temp);
					if ($link == $ext || in_array($ext, array('php', 'html', 'txt', 'htm'))) {
						$this->_indexate($link);
					}

				}

			}

		}

	}

}