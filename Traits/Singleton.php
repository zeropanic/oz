<?php

/**
 * This trait allows you to easily implement the Singleton design pattern
 * at Oz's style. 
 * @package  Traits
 * @author  zeropanic <zeropanic@myself.com>
 * @version  1.0
 * @namespace Oz\Traits
 */

namespace Oz\Traits;

trait Singleton
{
	/**
     * $_instance will store the instance of the class, value at start
     *            is null and will be type of your class after the call
     *            to getInstance() method.
     * @var null at start.
     * @access private static
     */
	private static $_instance = null;
	
    /**
     * getInstance instantiate your class if $_instance is null and
     *             return it, it just return if $_instance isnt null.
     * @return  mixed reference of your class instance
     */
	static function &getInstance()
    {
        if(is_null(static::$_instance)) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

    /**
     * __construct private constructor for Singleton design pattern.
     * @access  private
     * @return  mixed instance of your class
     */
    private function __construct(){}
    
    /**
     * __clone private cloning magic method for Singleton design
     *         pattern reinforcement.
     * @access  private
     * @return void
     */
    private function __clone(){}

}

?>
