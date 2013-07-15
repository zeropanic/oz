<?php

namespace Oz\Loader\Autoloader;

class NamespaceAutoloader implements AutoloaderInterface
{
	private $_namespacesArray = array();

	public function autoload($class)
	{
		$path = OZ_ROOT.DS.str_replace(NS, DS, $class).'.php';

		if(is_file($path))
		{
			require_once $path;
			return true;
		}

		foreach ($this->_namespacesArray as $namespace=>$dir)
		{
			$regexPattern = '#'.$namespace.'#';

			if (preg_match($regexPattern, $class))
			{
				$cleanClass = ltrim($class, $namespace.NS);
				$pathToClass = $dir.DS.str_replace(NS, DS, $cleanClass).'.php';

				if (is_file($pathToClass))
				{
					require_once $pathToClass;
					return true;
				}
			}
		}
	}

	public function registerNamespace($namespace, $dir)
	{
		if (!isset($this->_namespacesArray[$namespace]))
		{
			$this->_namespacesArray[$namespace] = $dir;
		}

		return $this;
	}

	public function unregisterNamespace($namespace)
	{
		if (isset($this->_namespacesArray[$namespace]))
		{
			unset($this->_namespacesArray[$namespace]);
		}

		return $this;
	}
}
