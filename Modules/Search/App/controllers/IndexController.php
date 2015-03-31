<?php

class Modules_Search_IndexController extends Zend_Controller_Action {
	
	/**
	 * Zend_Search_Lucene
	 *
	 * @var Zend_Search_Lucene
	 */
	protected $_indexHandle;
	
	public function init() {

		if (is_file(TEMP_PATH . '/Search/write.lock.file')) {
			$this->_indexHandle = Zend_Search_Lucene::open(TEMP_PATH . '/Search');
		}
		else {
			$this->_indexHandle = Zend_Search_Lucene::create(TEMP_PATH . '/Search');
		}
		Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive());
		
	}
	
	public function indexAction() {
		
		if ($this->hasParam('text')) {
			
			$userQuery = Zend_Search_Lucene_Search_QueryParser::parse($this->getParam('text'));
			$hits = $this->_indexHandle->find($userQuery);
		
			$result = array();
			
			foreach ($hits as $hit) {
				
				if (mb_strlen($hit->content) > 200) {
					$firstWord = current(explode(' ', $this->getParam('text')));
	
					$posWord = mb_strpos($hit->content, $firstWord);
					
					$before = mb_substr($hit->content, $posWord - 100, 100); // 20 символов до слова
					$after = mb_substr($hit->content, $posWord, 100); // 100 символов после слова
				
				    $text = '...' . $before . $after . '...';
				}
				else {
					$text = $hit->content;
				}
				
				array_push($result, array(
					'title' => $hit->title, 
					'url'	=> $hit->url,
					'content'	=> $userQuery->htmlFragmentHighlightMatches($text),// $userQuery->htmlFragmentHighlightMatches($hit->content)
				));
				
			}
			
			$this->view->result = $result;

		}
		
		$this->view->text = $this->getParam('text');
		
	}

}