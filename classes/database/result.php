<?php

defined('SYSPATH') OR die('No direct script access.');

abstract class Database_Result extends Kohana_Database_Result {

    /**
     *
     * @param type $primaryKey имя ключа массвиа
     * @param type $nameNeededValue имя поля массива
     * @return type
     */
    public function showAsArray($primaryKey = null, $nameNeededValue = null)
    {
        $array = $this->as_array();

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
