<?php

/**
 * Description of element
 *
 * @author seyfer
 */
class Structure_Element extends Database_Result {

    public function __destruct()
    {
        return parent::__destruct();
    }

    public function current()
    {
        return parent::current();
    }

    public function seek($position)
    {
        return parent::seek($position);
    }

    /**
     * не используется
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
