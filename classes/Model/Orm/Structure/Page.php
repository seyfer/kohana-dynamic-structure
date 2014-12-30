<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Description of Pgae
 *
 * @author alex
 */
class Model_ORM_Structure_Page extends Model_ORM_Structure {

    private $article;
    private $data;

    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * подготовить данные
     * @param type $struct
     * @return array
     */
    protected function prepareDataSet($struct)
    {

        //Пробежались по дереву
        foreach ($struct as $cat) {
            $categ         = $cat->as_array();
            $this->article = $cat->article;

            if ($this->checkArticle($this->data)) {
                $dataSet[$categ['id']]              = $categ;
                $dataSet[$categ['id']]['link']      = $cat->article->link;
                $dataSet[$categ['id']]['type_link'] = '';

                if ($dataSet[$categ['id']]['link']) {
                    if ($dataSet[$categ['id']]['link'][0] == '/') {
                        $dataSet[$categ['id']]['type_link'] = 'int';
                    } else {
                        $dataSet[$categ['id']]['type_link'] = 'ext';
                    }
                }
                $dataSet[$categ['id']]['children'] = array();
            }
        }

        return $dataSet;
    }

    /**
     * Проверяем используемые статьи
     * @param type $data
     */
    protected function checkArticle($data)
    {
        $settings = Kohana::$config->load('settings/structure')->as_array();

        $result = true;

        foreach ($settings as $setting) {
            if (isset($data[$setting])) {
                $result &= ($this->article->$setting == $data[$setting] ||
                        $this->article->$setting == '*');
            } else {
                $result &= (bool) $this->article->$setting;
            }
        }

        return $result;
    }

}
