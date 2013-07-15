<?php

namespace Oz\Loader\Autoloader;

abstract class AutoloaderFactory
{
	const AUTOLOAD_FUNC = 'autoload';

	protected static $autoloadersArray = array();

	static function registerAutoload(AutoloaderInterface $autoloader, $function = self::AUTOLOAD_FUNC)
	{
		$autoloaderClass = get_class($autoloader);
		
		if (!isset(static::$autoloadersArray[$autoloaderClass]))
		{
			static::$autoloadersArray[$autoloaderClass] = $autoloader;
			return spl_autoload_register(array($autoloader, $function));
		}
	}

	static function unregisterAutoload(AutoloaderInterface $autoloader, $function = self::AUTOLOAD_FUNC)
	{
		$autoloaderClass = \get_class($autoloader);

		if (isset(static::$autoloadersArray[$autoloaderClass]))
		{
			unset(static::$autoloadersArray[$autoloaderClass]);
			return spl_autoload_unregister(array($autoloader, $function));
		}
	}

	static function getAutoloader($autoloaderClass)
	{
		if (isset(static::$autoloadersArray[$autoloaderClass]))
		{
			return static::$autoloadersArray[$autoloaderClass];
		}

		return null;
	}
}

?>