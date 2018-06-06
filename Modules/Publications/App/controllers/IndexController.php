<?php

class Modules_Publications_IndexController extends Zend_Controller_Action
{

    /**
     * Модель списка типов публикаций
     *
     * @var Modules_Publications_Model_List
     */
    protected $modelList;

    /**
     * Модель полей публикаций
     *
     * @var Modules_Publications_Model_Fields
     */
    protected $modelFields;

    /**
     * Модель с самими публикациями
     *
     * @var Modules_Publications_Model_Table
     */
    protected $model;

    /**
     * Название таблицы модели
     *
     * @var string
     */
    protected $name;

    /**
     * ID маршрута к которому привязаны публикации
     *
     * @var int
     */
    protected $routeId = null;

    /**
     * Информация о текущем типе публикаций
     *
     * @var Zend_Db_Row
     */
    protected $currentPublicationType;


    public function init()
    {
        if ($this->name) {
            $this->_modelList = new Modules_Publications_Model_List();
            $this->_modelFields = new Modules_Publications_Model_Fields();
            $this->_model = new Modules_Publications_Model_Table($this->name);

            /* Устанавливаем рубрику к которой привязаны публикации */
            $this->_routeId = Zend_Registry::get('RouteCurrentId');

            if ($this->getParam('route_id')) {
                $this->_routeId = $this->getParam('route_id');
            }
            if ($this->getParam('skip_route')) {
                $this->_routeId = null;
            }

            $this->view->route_id = $this->_routeId;
            $this->_model->setRouteId($this->_routeId);

            /* Находим текущий тип публикаций */
            $this->_currentPublicationType = $this->_modelList->getRubricInfo($this->name);

            if ($this->_currentPublicationType) {
                $this->view->pub_rubric_id = $this->_currentPublicationType->rubric_id;
            } else {
                throw new Exception('Тип публикации "' . $this->name . '" не найден');
            }
        }
    }

    /**
     * @description Вывод списка публикаций постранично
     *
     */
    public function indexAction()
    {
    }

    /**
     * Формирование массива данных с учётом пейджинга
     *
     * @param object $data			Объект с данными для разбивки по страницам
     * @param object $onPage		Количество элементов на странице
     * @param object $pageNumber	Текущая страница
     * @return object				Объект пейджинатора
     */
    protected function _getPaginator($select, $onPage, $pageNumber)
    {
        if ($this->_routeId) {
            $select = $select->where('route_id = ?', $this->_routeId);
        }

        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setCurrentPageNumber($pageNumber);
        $paginator->setItemCountPerPage($onPage);

        return $paginator;
    }
}
