<?php

namespace Oz\App;

use \Oz\Config\Ini;

interface AppInterface
{
	public function init();

	public function setPath($pathKey, $path);

	public function getpath($pathKey);

	public function run();
}

?>