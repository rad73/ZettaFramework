<?php

/**
 * Хлебные крошки
 *
 */
class Zetta_View_Helper_Breadcrumb extends Zend_View_Helper_Abstract {

    public function breadcrumb($separator = '') {

    	$modelRouter = Modules_Router_Model_Router::getInstance();
		$currentRoute = $modelRouter->current();
		$parentsId = array_reverse($modelRouter->getParentsId($currentRoute['route_id']));

		$return = '';
		foreach ($parentsId as $parent) {

			$item = $modelRouter->getItem($parent);
			$return .= '<a href="' . $this->view->url(array('route_id' => $item['route_id'])) . '">' . $this->view->escape($item['name']) . '</a>' . $separator;

		}

		return $return . '<span>' . $currentRoute['name'] . '</span>';

    }

}