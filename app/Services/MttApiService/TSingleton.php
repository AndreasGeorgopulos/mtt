<?php

namespace App\Services\MttApiService;

trait TSingleton {
    private static $instance = null;

    private function __construct() {}

    public static function getInstance()
    {
        if (self::$instance == null) {
            $classname = get_class();
            self::$instance = new $classname;
        }
        return self::$instance;
    }
}