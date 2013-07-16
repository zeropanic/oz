<?php

/**
 * Request stores the datas fetched by the dispatcher and the router.
 * It can set/get a controller and/or an action with a name and a scheme.
 * it can also set/get/test the url params fetched by the router.
 * Getters allows you to fetch theses datas easily with a public access.
 * @package  Request
 * @author   zeropanic <zeropanic@myself.com>
 * @version  1.0
 * @namespace Oz\Http
 * @implements \Oz\Http\Request\RequestInterface
 * @uses  \Oz\Http\Request\RequestInterface the interface to implements
 *        for building a correct Request class
 */

namespace Oz\Http;

use \Oz\Http\Request\RequestInterface;

class Request implements RequestInterface
{
    /**
     * $urlParams stores the url params fetched by the router
     * @access  protected
     * @var array
     */
    protected $urlParams = array();

    /**
     * $module stores the module name
     * @access  protected
     * @var string
     */
    protected $module;

    /**
     * $controller stores the controller name
     * @access  protected
     * @var string
     */
    protected $controller;

    /**
     * [$controllerClass stores the controller class
     * @access  protected
     * @var string
     */
    protected $controllerClass;

    /**
     * $action stores the action name
     * @access  protected
     * @var string
     */
    protected $action;

    /**
     * $actionFunction stores the action method
     * @access  protected
     * @var string
     */
    protected $actionFunction;


    /**
     * setParams allows you to set and Url param. Its generally used
     * by the router after the routing process.
     * @access  public
     * @param string $name  name of the param
     * @param string $value value of the param
     * @return  void
     */
    public function setParam($name, $value)
    {
        $this->urlParams[$name] = $value;
    }

    /**
     * getParam allows you to retrieve and Url param. Its generally used
     * by your controllers. Be careful and test if the param exists with
     * the hasParam method before trying to retrive it. Else y'oull have
     * an error.
     * @access  public
     * @param string $name  name of the param
     * @return  string value of the param supplied
     */
    public function getParam($name)
    {
        return $this->urlParams[$name];
    }

    /**
     * hasParam checks wether an url param exists or not
     * @access  public
     * @param  string  $name name of the param
     * @return boolean       true if it exists, false if not
     */
    public function hasParam($name)
    {
        return isset($this->urlParams[$name]);
    }

    /**
     * setModule is the setter for the $module attribute
     * @access  public
     * @param string $module name of your module
     * @return  voic
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * getModule is the getter for the $module attribute
     * @access  public 
     * @return string $module attribute
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * setController is the setter for $controller attribute. If you
     * supply a scheme, it will also set the controller class attribute
     * depending on the supplied scheme.
     * @access  public
     * @param string $name   name of the controller
     * @param string $scheme scheme of class definition if you want to
     *                       set it in a row.
     * @return  void
     */
    public function setController($name, $scheme = null)
    {
        if (!is_null($scheme)) {
            $this->setControllerClass($name, $scheme);
        }

        $this->controller = $name;
    }

    /**
     * getController is the getter for $controller attribute
     * @access  public
     * @return string the $controller attribute
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * setControllerClass is the setter for the $controllerClass attribute.
     * It uses a scheme to define the class name. You should call setModule
     * method before calling setControllerClass method because this method
     * needs the $module attribute so you have to ensure one module is set
     * else your controllerClass attribute will be corrupted.
     * @access  public
     * @param string $controller name of your controller
     * @param string $scheme     scheme of the class definition of your
     *                           controller, basically, it looks like:
     *                           module{module}/{controller}Controller
     * @return  void
     */
    public function setControllerClass($controller, $scheme)
    {
       $this->controllerClass = str_replace('{controller}', $controller, $scheme);
       $this->controllerClass = str_replace('{module}', $this->module, $this->controllerClass);

    }

    /**
     * getControllerClass is the getter for the $controllerClass attribute
     * @access  public
     * @return string $controllerClass attribute
     */
    public function getControllerClass()
    {
        return $this->controllerClass;
    }

    /**
     * setAction is the setter for the $action attribute. If you supply
     * a scheme, then the $actionFunction attribute will be set in a row.
     * @access  public
     * @param string $name   name of the action
     * @param string $scheme scheme of the action
     * @return  void
     */
    public function setAction($name, $scheme = null)
    {
        if (!is_null($scheme) && is_string($scheme)) {
            $this->setActionFunction($name, $scheme);
        }

        $this->action = $name;
    }

    /**
     * getAction is the getter for the $action attribute
     * @access  public
     * @return string $action attribute
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * setActionFunction is the setter for $actionFunction attribute it
     * uses a scheme to define the action function
     * @access  public
     * @param string $function function name
     * @param string $scheme   scheme of your actionFunction definition
     *                         basically it looks like this:
     *                         {action}Action
     * @return  void
     */
    public function setActionFunction($function, $scheme)
    {
        $this->actionFunction = str_replace('{action}', $function, $scheme);
    }

    /**
     * getActionFunction is the getter for $actionFunction attribute
     * @access  public
     * @return string $actionFunction attribute
     */
    public function getActionFunction()
    {
        return $this->actionFunction;
    }

}