<?php

/**
 * Bootstrap для модуля Modules_Search
 * 
 * @author Александр Хрищанович
 *
 */
class Modules_Search_Bootstrap extends Zetta_BootstrapModules {

	public function bootstrap() {
		
		Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');
		Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive ());

		parent::bootstrap();
	}
	
}