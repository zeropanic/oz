<?php

namespace Oz\Loader\Autoloader;

class NamespaceAutoloader implements AutoloaderInterface
{
	private $_namespacesArray = array();

	public function autoload($class)
	{
		//On essaye d'abord de transformer le nom de la classe en chemin vers son fichier.
		//Ceci fonctionne seulement si les classes sont bien nommées.
		$path = OZ_ROOT.DS.str_replace(NS, DS, $class).'.php';

    	if(is_file($path))
    	{
        	require_once $path;
        	//on s'arrete là si ça a marché.
        	return true;
    	}

    	//Si la première tentative échoue, on regarde si il ne s'agit pas d'un namespace spécial
    	//qu'on a enregistré nous même avec NamespaceAutoloader::registerNamespace().
    	//on boucle donc sur tout les namespaces enregistrés pour vérifier si aucun correspond à la classe.
		foreach ($this->_namespacesArray as $namespace=>$dir)
		{
			$regexPattern = '#'.$namespace.'#';

			//si le namespace principal correspond à la classe
			if (preg_match($regexPattern, $class))
			{
				//On enlève le namespace principal du nom de la classe
				$cleanClass = ltrim($class, $namespace.NS);
				//et on transforme les sous-namespaces + nom de classe en chemin vers le fichier
				//a partir du répertoire précisé lors de l'enregistrement du namespace.
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