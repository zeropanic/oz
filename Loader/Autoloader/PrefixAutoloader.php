<?php

namespace Oz\Loader\Autoloader;

class PrefixAutoloader implements AutoloaderInterface
{
	protected static $prefixesArray = array();

	public function autoload($class)
	{
		//On essaye d'abord de transformer le nom de la classe en chemin vers son fichier
		//Ceci fonctionne seulement si les classes sont bien nommées.
		$path = ROOT.DS.str_replace('_', DS, $class).'.php';

    	if(is_file($path))
    	{
        	require_once $path;
        	//on s'arrete là si ça a marché.
        	return true;
    	}

    	//Si la première tentative échoue, on regarde si il ne s'agit pas d'un préfixe
    	//qu'on a enregistré nous même avec PrefixAutoloader::registerPrefix().
    	//on boucle donc sur tout les namespaces enregistrés pour vérifier si aucun correspond à la classe
		foreach (static::$prefixesArray as $prefix=>$dir)
		{
			$regexPattern = '#'.$prefix.'#';

			//si la classe commence par le prefixe actuel
			if (preg_match($regexPattern, $class))
			{
				//On enlève le prefixe du nom de la classe
				$cleanClass = ltrim($class, $prefix);
				//et on transforme le nom de classe en chemin vers le fichier
				//a partir du répertoire précisé lors de l'enregistrement du prefixe
				$pathToClass = $dir.DS.str_replace(NS, DS, $cleanClass).'.php';
				//si le fichier existe on l'inclu.
				if (is_file($pathToClass))
				{
					require_once $pathToClass;
					return true;
				}
			}
		}
	}

	public function register($prefix, $dir)
	{
		if (!isset(static::$prefixesArray[$prefix]))
		{
			static::$prefixesArray[$prefix] = $dir;
		}
	}

	public function unregister($prefix)
	{
		if (isset(static::$prefixesArray[$prefix]))
		{
			unset(static::$prefixesArray[$prefix]);
		}
	}
}