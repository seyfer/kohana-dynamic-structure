<?php

defined('SYSPATH') or die('No direct access allowed.');

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

    public function findArticle($id)
    {
        $article = $this->where('parent_id', '=', $id)
                ->find();

        $result = $article->as_array();

        $result["title"] = $article->structure->title;
        $result["img"]   = $article->structure->img;


        return $result;
    }

}
