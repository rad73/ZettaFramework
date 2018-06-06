<?php

/**
 * @description {description}
 *
 */
class Publications_{Base}Controller extends Modules_Publications_IndexController
{
    protected $name = '{base}';

    /**
     * @description Вывод списка публикаций
     *
     */
    public function indexAction()
    {
        $sql = $this->_model->select()
            ->order('sort')
            ->where('active = 1');

        if ($this->getParam('limit')) {
            $sql = $sql->limit($this->getParam('limit'));
        }

        $this->view->data = $this->_model->fetchAll($sql);
    }
}
