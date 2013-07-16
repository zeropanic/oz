<?php

/**
 * App class allows you to initialize whatever you want with the init
 * method. It also run the application by loading the dispatcher and
 * runing the dispatching process.
 * @package  App
 * @author  zeropanic <zeropanic@myself.com>
 * @version  1.0
 * @namespace Oz
 * @implements \Oz\App\AppInterface
 * @uses  \Oz\Di\Dic our dependency injection container
 * @uses  \Oz\App\AppInterface the interface we got to implements
 * @uses  \Oz\Traits\ConfigReader the trait to read config files easily
 * @uses  \Oz\Exception as OzException main Exception class of the framework
 *        useful to catch all exceptions thrown by the framework
 * @uses  \Oz\App\Exception the App package Exception class.
 * @uses  \Oz\App\Exception\InvalidArgument the InvalidArgument Exception
 *        class for the App package
 */

namespace Oz;

use
    /* Static dependencies */
    \Oz\Di\Dic,
    \Oz\App\AppInterface,
    \Oz\Traits\ConfigReader,
    /* Exceptions */
    \Oz\Exception as OzException,
    \Oz\App\Exception,
    \Oz\App\Exception\InvalidArgument;


class App implements AppInterface
{
    /**
     * @uses  \Oz\Traits\ConfigReader the trait for reading config files.
     */
    use ConfigReader;

    /**
     * $paths store all paths for the application such as cache, kernel etc.
     * @var array
     * @access  protected
     */
    protected $paths = array();

    /**
     * __construct our class constructor, set the config and hydrate the class
     * @access public
     * @return void
     */
    public function __construct($configPath)
    {
        $this->config = $this->parseConfig($configPath);
        $this->hydrate();
    }

    /**
     * hydrate will set predefined paths and constants of App.ini file
     * @access  protected
     * @return void
     */
    protected function hydrate()
    {
        $config = $this->getConfig();

        if (isset($config->app->path) && $config->app->path instanceof Ini) {
            foreach ($config->app->path as $key => $value){
                $this->setPath($key, $value);
            }
        }

        if (isset($config->app->constant) && $config->app->constant instanceof Ini) {
            foreach ($config->app->constant as $constant => $value){
                define($constant, $value);
            }
        }

        unset($config);
    }

    /**
     * init the application
     * @access  public
     * @return \Oz\App instance of the class for chaining with run method
     */
    public function init()
    {
        return $this;
    }

    /**
     * run the application by calling dispatcher->dispatch() method
     * @access  public
     * @return  void
     */
    public function run()
    {
        try {
            Dic::getInstance()->getService('dispatcher')->dispatch();
        } catch(OzException $e) {
            echo $e;
        }
    }

    /**
     * setPath set a path of the application, you can add whatever
     *         path you want by providing a key and a path.
     * @access  public
     * @param string $pathKey key to store the path
     * @param string $path    absolute path associated with the above key
     * @return  void
     */
    public function setPath($pathKey, $path)
    {
        $this->paths[$pathKey] = $path;
    }

    /**
     * getPath allows you to get a path defined with setPath
     * @access  public
     * @param  string $pathKey the key of the path you want to retrieve
     * @return mixed string of the path if one exists for the supplied key
     *               or false if none exists
     */
    public function getPath($pathKey)
    {
        return array_key_exists($pathKey, $this->paths) ? $this->paths[$pathKey] : false;
    }
}

?>
