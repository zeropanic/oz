<?php

namespace Oz\Loader;

interface LoaderInterface
{
	static function getInstance();
	
	public function loadPlugin($plugin);

	public function loadFile($file);

	public function loadClass($class);

	public function loadController($controller, $module = 'default');
}

?>