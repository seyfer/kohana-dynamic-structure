<?php

defined('SYSPATH') or die('No direct access allowed.');

class Model_ORM_Roles extends ORM {

    protected $_table_name  = 'roles';
    protected $_primary_key = 'id';

    /**
     * не используется
     * @param type $primaryKey имя ключа массвиа
     * @param type $nameNeededValue имя поля массива
     * @return type
     */
    public function showAsArray(Database_Result $result, $nameNeededValue = null)
    {
        $array = $result->as_array();

        $primaryKey = $this->primary_key();

        $key = 0;

        $result = array();

        foreach ($array as $value) {

            if (empty($primaryKey)) {
                $key++;
            }
            else {
                $key = $value->$primaryKey;
            }

            if (empty($nameNeededValue)) {
                $result[$key] = $value->as_array();
            }
            else {
                $result[$key] = $value->$nameNeededValue;
            }
        }

        return $result;
    }

}
