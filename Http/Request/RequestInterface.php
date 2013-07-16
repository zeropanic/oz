<?php

/**
 * The RequestInterface describes what it needs to build a correct
 * Request class.
 * @package  Request
 * @author   zeropanic <zeropanic@myself.com>
 * @version  1.0
 * @namespace Oz\Http\Request
 */

namespace Oz\Http\Request;

interface RequestInterface
{
	public function setParam($name, $value);

	public function getParam($name);

	public function hasParam($name);

	public function setModule($module);

	public function getmodule();

	public function setController($controller);

	public function getController();

	public function setControllerClass($controller, $scheme);

	public function getControllerClass();

	public function setAction($action, $scheme = null);

	public function setActionFunction($function, $scheme);

	public function getActionFunction();
}

?>