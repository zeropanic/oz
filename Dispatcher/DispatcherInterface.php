<?php

/**
 * DispatcherInterface describes what it needs to build a custom dispatcher.
 * @package  Dispatcher
 * @author  zeropanic <zeropanic@myself.com>
 * @version  1.0
 * @namespace Oz\Dispatcher
 */

namespace Oz\Dispatcher;

interface DispatcherInterface
{
	/**
	 * dispatch is the process of identifying resources asked by client,
	 * checking whether they exists or not, and launching the MVC with
	 * the ressources collected during the process.
	 * @return void
	 */
	public function dispatch();
}