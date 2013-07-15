<?php

namespace Oz\Traits;

trait Singleton
{
	private static $_instance = null;

	static function &getInstance()
    {
        if(is_null(static::$_instance))
        {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

    private function  __construct(){}
    
    private function __clone(){}

}

?>