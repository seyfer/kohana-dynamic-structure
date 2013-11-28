<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * модель статьи
 */
class Model_ORM_Articles extends ORM {

    protected $_table_name  = 'articles';
    protected $_primary_key = 'id';

    /**
     * связь с структурой
     * @var type
     */
    protected $_belongs_to = array(
        'structure' => array(
            'model'       => 'ORM_Structure',
            'foreign_key' => 'parent_id',
        )
    );

    /**
     *
     * @param type $id
     * @return type
     */
    public function findArticle($id)
    {
        $article = $this->findByParent($id);

        $result = $article->as_array();

        $result["title"] = $article->structure->title;
        $result["img"]   = $article->structure->img;

        return $result;
    }

    /**
     * найти по ид структуры
     * @param type $parentId
     * @return \Model_ORM_Articles
     */
    public function findByParent($parentId)
    {
        $this->where('parent_id', '=', $parentId)->find();
        $this->parent_id = $parentId;

        return $this;
    }

    /**
     * подготовить значения
     * @param array $post
     */
    private function preparePostForSave($post)
    {
        $keyes = array(
            'text', 'link', 'language', 'visible', 'namehtml', 'role'
        );

        //Убираем лишнии интервалы
        $post['text'] = str_replace('<p>&nbsp;</p>', '', $post['text']);

        foreach ($keyes as $key) {
            if (isset($post[$key])) {
                $val = $post[$key];
            }

            if ($key === 'visible') {
                $val = isset($post[$key]) ? 1 : 0;
            }

            $this->set($key, $val);
        }
    }

    /**
     * сохранить подготовленные значения
     * @param type $post
     */
    public function savePost($post)
    {
        $this->preparePostForSave($post);

        $this->save();
    }

}
