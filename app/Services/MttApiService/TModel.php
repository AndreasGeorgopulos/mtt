<?php

namespace App\Services\MttApiService;

trait TModel {

    function __construct(object $item = null)
    {
        if (!$item) return;

        foreach (get_class_vars(get_class($this)) as $field => $value) {
            if (isset($item->$field)) $this->$field = $item->$field;
        }
    }

    public function __get ($field)
    {
        return isset($this->$field) ? $this->$field : null;
    }

    public function __set ($field, $value)
    {
        if (isset($this->$field)) $this->$field = $value;
    }

    public function toArray () : array {
        $array = [];
        foreach (get_class_vars(get_class($this)) as $field => $value) {
            if (isset($this->$field)) $array[$field] = $this->$field;
        }
        return $array;
    }

}