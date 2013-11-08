<?php defined('SYSPATH') or die('No direct access allowed.');


class Model_ORM_Structure extends ORM_MPTT{
    protected $_table_name = 'structure';
    protected $_primary_key = 'id';
    
    protected $_has_one = array(
        'article' => array(
                'model' => 'ORM_Articles',
                'foreign_key' => 'parent_id',
            ),
        );
    
    /**
     * Переобъявили, так как по другому перемещение внутри узла я сделать не смог
     */
    protected function lock()
    {
        $q = 'LOCK TABLE '.$this->_db->quote_table($this->_table_name).' WRITE';

        if ($this->_object_name)
        {
            $q.= ', '.$this->_db->quote_table($this->_table_name);
            $q.= ' AS '.$this->_db->quote_column($this->_object_name).' WRITE';
        }

        $this->_db->query(NULL, $q, TRUE);
    }
    
    public static function getStruct($dataSet){
        //создали элемент для цикла
        $elements = $dataSet;
        $result = array();
        foreach ($elements as $id => $value)
        {
            if ($value['parent_id'] > 0 && isset($elements[$value['parent_id']]))
            {
                $elements[$id]['parent']                    = & $elements[$value['parent_id']];
                $elements[$value['parent_id']]['children'][] = & $elements[$id];
                if(isset($elements[$id]['countChildren']))
                    $elements[$id]['countChildren']++;
                else
                    $elements[$id]['countChildren']=0;
            }
            else
            {
                $result[$id] = & $elements[$id];
            }
        }
        
        
        
        return $result;
    }
    
    public function getTreeAsArray(){
        
        $this->clear();
        
        $struct = $this->fulltree()->as_array();
        
        
        $dataSet = array();
        
        //Пробежались по дереву
        foreach ($struct as $cat)
        {
            $categ = $cat->as_array();
            $categ['visible'] = $cat->article->visible;
            $dataSet[$categ['id']] = $categ;
            $dataSet[$categ['id']]['children'] = array();
            
        }
        
        return self::getStruct($dataSet);
    }
}