<?php

class Modules_Publications_AdminController extends Zend_Controller_Action {

	/**
	 * Модель типов публикаций
	 *
	 * @var Modules_Publications_Model_List
	 */
	protected $_modelList;

	/**
	 * Модель полей публикаций
	 *
	 * @var Modules_Publications_Model_Fields
	 */
	protected $_modelFields;

	/**
	 * Модель публикаций
	 *
	 * @var Modules_Publications_Model_Table
	 */
	protected $_modelPublications;

	/**
	 * Текущий тип публикаций
	 *
	 * @var Zend_Db_Row
	 */
	protected $_rubric;


	public function init() {

		if (false == Zetta_Acl::getInstance()->isAllowed('admin_module_publications')) {
			throw new Exception('Access Denied');
		}

		$this->_modelList = new Modules_Publications_Model_List();
		$this->_modelFields = new Modules_Publications_Model_Fields();

		$this->_helper->getHelper('AjaxContext')
        	->addActionContext('index', 'html')
        	->addActionContext('add', 'html')
        	->addActionContext('delete', 'json')
        	->addActionContext('fields', 'html')
        	->addActionContext('routes', 'html')
        	->addActionContext('addfield', 'html')
        	->addActionContext('sortfields', 'json')
        	->addActionContext('deletefield', 'json')
        	->addActionContext('view', 'html')
        	->addActionContext('sortpublications', 'json')
        	->addActionContext('addpublication', 'html')
        	->addActionContext('deletepublication', 'json')
            ->initContext();

        if ($rubric_id = $this->getParam('rubric_id')) {

			$this->_rubric = $this->view->rubric = $this->_modelList->getRubricInfo($rubric_id);
			$this->_modelPublications = new Modules_Publications_Model_Table($this->view->rubric->table_name);

        }

	}

	public function indexAction() {
		$this->view->list = $this->_modelList->fetchAll();
	}

	/**
	 * Добавляем новый тип публикации
	 *
	 */
	public function addAction() {

		$form = new Zetta_Form(Zend_Registry::get('config')->Publications->form->rubric);

		if ($this->_rubric && $rubric_id = $this->_rubric->rubric_id) {
			$form->setDefaults($this->_rubric->toArray());
		    $form->getElement('table_name')->setAttrib('disabled', 'disabled');
		}

		if (!sizeof($_POST) || !$form->isValid($_POST)) {
		    $this->view->form = $form;
		}
		else {

			$arrayData = array(
				'name'			=> $form->getValue('name'),
				'table_name'	=> $form->getValue('table_name'),
			);

			if ($this->_rubric) {
				$this->_modelList->update($arrayData, $this->_modelList->getAdapter()->quoteInto('rubric_id = ?', $rubric_id));
			}
			else {
				$this->_modelList->insert($arrayData);
				$this->_generateFiles($form->getValue('table_name'), $form->getValue('name'));
			}

			$this->renderScript('admin/addComplete.ajax.phtml');

		}

	}

	/**
	 * Удаляем тип публикаций
	 *
	 */
	public function deleteAction() {

		if (!$this->_rubric) throw new Exception('rubric_id не определён');

		$where = $this->_modelList->getAdapter()->quoteInto('rubric_id = ?', $this->_rubric->rubric_id);
		$this->_modelList->delete($where);

	}

	/**
	 * Выводим список маршрутов к которому подключён раздел
	 *
	 */
	public function routesAction() {

		if ($rubric_id = $this->getParam('rubric_id')) {

			$rubricInfo = $this->_modelList->getRubricInfo($rubric_id);

			$this->view->routes = Modules_Router_Model_Router::getInstance()->fetchAll(
				$sql = Modules_Router_Model_Router::getInstance()->select()
					->setIntegrityCheck(false)
					->from(array('r' => Modules_Router_Model_Router::getInstance()->info('name')))
					->join(array('p' => $this->_modelPublications->info('name')), 'p.route_id = r.route_id', array())
					->group('r.route_id')
			);

			$this->view->rubric_id = $rubric_id;
			$this->view->publications = $this->_modelPublications->getWithoutRubrics($this->getParam('page', 1));
			$this->view->paginator = $this->view->publications;

		}

	}

	/**
	 * Вывод списка полей в конкретном типе публикаций
	 *
	 */
	public function fieldsAction() {

		if (!$this->_rubric) throw new Exception('rubric_id не определён');

		$this->view->fields = $this->_modelFields->getFieldsByRubric($this->_rubric->rubric_id);

	}

	/**
	 * Добавляем поле в тип публикации
	 *
	 */
	public function addfieldAction() {

		if (!$this->_rubric) throw new Exception('rubric_id не определён');

		$form = new Zetta_Form(Zend_Registry::get('config')->Publications->form->fields);

		if ($field_id = $this->getParam('field_id')) {
			$this->view->field_id = $field_id;
			$editData = $this->_modelFields->fetchRow($this->_modelFields->select()->where('field_id = ?', $field_id))->toArray();
			$form->setDefaults($editData);

			$form->getElement('name')->setAttrib('disabled', 'disabled');
		}

		if (!sizeof($_POST) || !$form->isValid($_POST)) {
		    $this->view->form = $form;
		}
		else {

			$arrayData = array(
				'rubric_id'		=> $this->_rubric->rubric_id,
				'name'	=> $form->getValue('name'),
				'title'	=> $form->getValue('title'),
				'type'	=> $form->getValue('type'),
				'validator'	=> $form->getValue('validator'),
				'default'		=> $form->getValue('default'),
				'errormsg'	=> $form->getValue('errormsg'),
				'list_values'	=> $form->getValue('list_values'),
				'hidden_front'	=> $form->getValue('hidden_front'),
			);

			if ($field_id) {
				$this->_modelFields->update($arrayData, $this->_modelFields->getAdapter()->quoteInto('field_id = ?', $field_id));
			}
			else {
				$this->_modelFields->insert($arrayData);
			}

			$this->renderScript('admin/addfieldComplete.ajax.phtml');

		}

	}

	/**
	 * Пересортировываем поля
	 *
	 * @param array $_REQUEST['data']		Массив field_id, sort c сортировкой
	 */
	public function sortfieldsAction() {

		foreach ($this->getParam('data') as $row) {

			$where = $this->_modelFields->getAdapter()->quoteInto('field_id = ?', $row['id']);

			$this->_modelFields->update(array(
				'sort'				=> $row['sort'],
			), $where);

		}

	}

	/**
	 * Удаление поля
	 *
	 * @param int $_REQUEST['field_id']		ID поля
	 */
	public function deletefieldAction() {

		if ($field_id = $this->getParam('field_id')) {

			$where = $this->_modelFields->getAdapter()->quoteInto('field_id = ?', $field_id);
			$this->_modelFields->delete($where);

		}

	}

	/**
	 * Просмотр добавленных публикаций
	 *
	 * @param int $_REQUEST['rubric_id']		ID рубрики в которую добавлены материалы
	 */
	public function viewAction() {

		if (!$this->_rubric) throw new Exception('rubric_id не определён');

		$this->_modelPublications->setRouteId($this->getParam('route_id'));
		$this->view->publications = $this->_modelPublications->fetchAll($this->_modelPublications->select()->order('sort')->order('publication_id'));
		$this->view->route_id = $this->getParam('route_id');
		$this->view->route = Modules_Router_Model_Router::getInstance()->getItem($this->getParam('route_id'));

		try {	// пробуем подключить пользовательский шаблон
			$this->renderScript('admin/view_' . $this->_rubric->rubric_id . '.ajax.phtml');
		}
		catch (Exception $e) { }

	}

	/**
	 * Пересортировываем поля
	 *
	 * @param array $_REQUEST['data']		Массив field_id, sort c сортировкой
	 */
	public function sortpublicationsAction() {

		if (!$this->_rubric) throw new Exception('rubric_id не определён');

		$data = $this->getParam('data');
		$this->_modelPublications->sort($data['current'], $data['next'], $data['prev']);

	}

	/**
	 * Удаляем публикацию
	 *
	 * @param int $_REQUEST['publication_id']
	 */
	public function deletepublicationAction() {

		if (!$this->_rubric) throw new Exception('rubric_id не определён');

		if ($publication_id = $this->getParam('publication_id')) {

			$where = $this->_modelPublications->getAdapter()->quoteInto('publication_id = ?', $publication_id);
			$this->_modelPublications->delete($where);

		}

	}

	/**
	 * Добавляем / изменяем публикацию
	 *
	 */
	public function addpublicationAction() {

		if (!$this->_rubric) throw new Exception('rubric_id не определён');

		$form = new Publications_Framework_Form($this->_rubric->table_name);

		if ($form->getElement('uniq_id') && $this->getParam('uniq_id')) {
			$form->getElement('uniq_id')->setValue($this->getParam('uniq_id'));
		}

		if ($publication_id = $this->getParam('publication_id')) {

			$this->view->publication_id = $publication_id;
			$editData = $this->_modelPublications->fetchRow($this->_modelPublications->select()->where('publication_id = ?', $publication_id))->toArray();

			$form->setDefaults($editData);

		}

		if ($route_id = $this->getParam('route_id')) {
			$this->view->route_id = $this->getParam('route_id');
		}

		if (!sizeof($_POST) || !$form->isValid($_POST)) {
		    $this->view->form = $form;
		}
		else {

			$post = $form->getPostData();

			if ($route_id) {
				$post['route_id'] = $route_id;
			}

			if ($publication_id) {
				$this->_modelPublications->update($post, $this->_modelFields->getAdapter()->quoteInto('publication_id = ?', $publication_id));
			}
			else {
				$this->_modelPublications->insert($post);
			}

			$this->renderScript('admin/addPublicationComplete.ajax.phtml');

		}

	}

	protected function _generateFiles($name, $description) {

		$fromFolder = MODULES_PATH . DS . 'Publications' . DS . 'CodeTemplates';
		$toFolder = HEAP_PATH . DS . 'Publications' . DS . 'App';

		$toController = $toFolder . DS . 'controllers' . DS . ucfirst($name) . 'Controller.php';
		$toViewFolder = $toFolder . DS . 'views' . DS . 'scripts' . DS . $name;

		// копируем контроллер
		System_Functions::Copy($fromFolder . DS . 'BaseController.php', $toController);

		// копируем view скрипты
		System_Functions::Copy($fromFolder . DS . 'views', $toFolder . DS . 'views' . DS . 'scripts' . DS . $name);

		$files = glob($toViewFolder . DS . '*.*', GLOB_NOSORT);
		$files[] = $toController;

		foreach ($files as $file) {

			$content = file_get_contents($file);
			$content = str_replace('{base}', $name, $content);
			$content = str_replace('{Base}', ucfirst($name), $content);
			$content = str_replace('{description}', $description, $content);

			file_put_contents($file, $content);

		}

	}

}