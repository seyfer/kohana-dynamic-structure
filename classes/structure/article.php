<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Description of article
 *
 * @author seyfer
 */
class Structure_Article {

    private $modelStructureArticle;

    public function __construct()
    {
        $this->modelStructureArticle = new Model_ORM_Articles();
    }

    /**
     * delete article by parent
     * @param type $parent_id
     * @return type
     */
    public function deleteByParent($parent_id)
    {
        $this->modelStructureArticle->clear();

        $ormArticle = $this->modelStructureArticle
                ->where('parent_id', '=', $parent_id)
                ->find();

        if ($ormArticle->loaded()) {
            return $ormArticle->delete();
        }
    }

}
