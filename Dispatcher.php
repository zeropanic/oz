<?php

/**
 * Dispatcher use the Request and the Router to identify ressources called
 * by the client request. When its done, it calls the frontcontroller, pass
 * it the request and starts the MVC.
 * @package  Dispatcher
 * @author  zeropanic <zeropanic@myself.com>
 * @version  1.0
 * @namespace Oz
 * @implements \Oz\Dispatcher\DispatcherInterface
 * @uses  \Oz\Di\Dic our dependency injection container
 * @uses  \Oz\Traits\IniConfigReader  our config reader trait
 * @uses  \Oz\Router\RouterInterface interface for passing the router to
 *        the constructor (dependency injection)
 * @uses  \Oz\Http\Request\RequestInterface interface for passing the
 *        request ton constructor (dependency injection)
 * @uses  \Oz\Dispatcher\DispatcherInterface interface for this dispatcher
 * @uses  \Oz\Dispatcher\Exception the dispatcher's package main exception
 * @uses  \Oz\Dispatcher\Exception\Logic the dispatcher's package Logic
 *        Exception class.
 */

namespace Oz;

use
    /* Static dependencies */
    \Oz\Di\Dic,
    \Oz\Traits\IniConfigReader,
    \Oz\Router\RouterInterface,
    \Oz\HTTP\Request\RequestInterface,
    \Oz\Dispatcher\DispatcherInterface,
    /* Exceptions */
    \Oz\Dispatcher\Exception,
    \Oz\Dispatcher\Exception\Logic,
    \oz\Dispatcher\Exception\BadMethodCall;

class Dispatcher implements DispatcherInterface
{
    /**
     * @uses  \Oz\Traits\IniConfigReader to read and parse ini files easily
     */
    use IniConfigReader;

    /**
     * $request will store the request instance
     * @access  protected
     * @var \Oz\Http\Request\RequestInterface
     */
    protected $request;

    /**
     * $router will store the router instance
     * @access  protected
     * @var \Oz\Router\RouterInterface
     */
    protected $router;

    
    /**
     * class constructor, set the config, router and request
     * @access  public
     * @param RouterInterface  $router         router instance
     * @param RequestInterface $request        request instance
     * @param string           $configFilePath path to the config file
     *                                         of the dispatcher
     * @return  void
     */
    public function __construct(RouterInterface   $router,
                                RequestInterface  $request,
                                                  $configFilePath)
    {
        $this->config   = $this->parseIniConfig($configFilePath);
        $this->router   = $router;
        $this->request  = $request;
    }

    /**
     * dispatch describe the dispatching process. Asking request and router
     * to identify the resources asked by the client request. It also check
     * whether a resource exists or not, and avoid problems by setting 
     * default values for some cases (errors 404 & 501). It can handle custom
     * controllerClass/actionFunction schemes. If no problem were found, it
     * calls the frontcontroller and ask it to run the MVC.
     * @access  public
     * @throws  \Oz\Dispatcher\Exception\Logic If the controller or the
     *          controller class is missing.
     * @throws  \Oz\Dispatcher\Exception\badMethodCall If the action method
     *          is missing inside of your controller.
     * @return void
     */
    public function dispatch()
    {
        $module = $this->router->fetchModule();
        $modulesDir = Dic::getInstance()->getService('app')->getPath('modules');

        if ($module && is_dir($modulesDir.DS.$module)){
            $this->request->setModule($module);
        } else {
            $this->request->setModule($this->config->dispatcher->default->name->module);
        }

        $moduleDir = $modulesDir.DS.$this->request->getModule();
        $moduleCfg = $moduleDir.DS.'module.ini';

        if (is_file($moduleCfg)) {
            $this->moduleConfig = $this->parseConfig($moduleCfg, true);
        } else {
            $this->moduleConfig = $this->config->dispatcher->default;
        }

        $routeFile = $moduleDir.DS.$this->moduleConfig->module->file->routes;

        if (is_file($routeFile)) {
            $routes = \simplexml_load_file($routeFile);

            foreach ($routes->route as $route) {
                $this->router->addRoute($route['controller'], $route['action'], $route['route']);
            }
        }

        if ($this->router->parseRoutes()) {
            $controller = $this->router->getController();
            $action     = $this->router->getAction();
            $params     = $this->router->getUrlParams();
        } else {
            $controller = $this->moduleConfig->module->error404->controller;
            $action     = $this->moduleConfig->module->error404->action;
            $params     = array();
        }

        $cache = Dic::getInstance()->getService('cache')->deleteCache();


        $controllerClassScheme = $this->moduleConfig->module->scheme->controller->class;
        $controllerFileScheme  = $this->moduleConfig->module->scheme->controller->file;
        $actionFunctionScheme  = $this->moduleConfig->module->scheme->action;

        $this->request->setController($controller, $controllerClassScheme);
        $this->request->setAction($action, $actionFunctionScheme);

        $baseControllerPath = $moduleDir.$this->moduleConfig->module->directory->controllers;
        $controllerFile = str_replace('{controller}', $controller, $controllerFileScheme);
        $controllerPath = $baseControllerPath.DS.$controllerFile;

        if (is_file($controllerPath)) {

            require_once $controllerPath;

            if (!class_exists($this->request->getControllerClass())){
                throw new Logic(
                    'Dispatcher expect controller class ('.$this->request->getControllerClass()
                    .') in your controller file ('.$controllerPath.')'
                );
            }
        } else {
            throw new Logic('Dispatcher cant find your controller file ('.$controllerPath.')');
        }

        $reflection = new \ReflectionClass($this->request->getControllerClass());
        if (!$reflection->hasMethod($this->request->getActionFunction())) {
            throw new BadMethodCall('Missing action '.$this->request->getActionFunction()
                .' in your controller ('.$this->request->getControllerClass().')');
        }

        try {
            //Dic::getInstance()->getService('frontcontroller')->startMvc();
        } catch (\Oz\Exception $e) {
            $e->printOrReport();
        }
    }

}

?>