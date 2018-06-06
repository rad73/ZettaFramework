<?php

class Modules_Seo_Model_Seo extends Zetta_Db_Table
{
    protected $name = 'seo';
    
    /**
     * Сохраняем данные по seo
     *
     * @param array $array
     * @param string $url
     * @return mixed         ID добавленной / изменённой строки
     */
    public function save($array, $url)
    {
        $exist = $this->fetchRow(
        
            $this->select()
            ->where('url = ?', $url)
        );
        
        $array['url'] = $url;
        
        if (sizeof($exist)) {
            return $this->update($array, $this->getAdapter()->quoteInto('seo_id = ?', $exist->seo_id));
        } else {
            return $this->insert($array);
        }
    }
    
    public function findByUrl($url)
    {
        return $this->fetchRow($this->select()->where('url = ?', $url));
    }
}
