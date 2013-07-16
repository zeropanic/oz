<?php

/** The AppInterface describes what it needs to build an application class.
 * @package  App
 * @author  zeropanic <zeropanic@myself.com>
 * @version  1.0
 * @namespace Oz\App
 */

namespace Oz\App;

interface AppInterface
{
    /**
     * init the application
     * @access  public
     * @return \Oz\App instance of the class for chaining with run method
     */
	public function init();

    /**
     * setPath set a path of the application, you can add whatever
     *         path you want by providing a key and a path.
     * @access  public
     * @param string $pathKey key to store the path
     * @param string $path    absolute path associated with the above key
     * @return  void
     */
	public function setPath($pathKey, $path);

    /**
     * getPath allows you to get a path defined with setPath
     * @access  public
     * @param  string $pathKey the key of the path you want to retrieve
     * @return mixed string of the path if one exists for the supplied key
     *               or false if none exists
     */
	public function getpath($pathKey);

    /**
     * run the application by calling dispatcher->dispatch() method
     * @access  public
     * @return  void
     */
	public function run();
}

?>