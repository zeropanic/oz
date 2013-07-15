<?php

namespace Oz;

use /* Exceptions */
    \Oz\Loader\Exception\Logic,
    \Oz\Loader\Exception\InvalidArgument;

class Loader
{
    const VERIFY_CLASS      = true;

    const NO_RECURSIVE      = 1;

    const RECURSIVE_LOAD    = 2;

    public function loadClass($class, $separator = '_', $verifyClass = false)
    {
        $path = OZ_ROOT.DS.str_replace($separator, DS, $class).'.php';
        
        if (is_file($path)) {

            require_once($path);

            if ($verifyClass === false) {
                return true;
            }

            if (class_exists($class)) {
                return true;
            } else {
                throw new Logic('Impossible to find the expected class '.$class.' located at '.$path.'.');
            }

        } else {
            throw new InvalidArgument('Impossible to find class file located at '.$path.'.');
        }
    }

    public function loadDir($dir, $recursive = self::NO_RECURSIVE)
    {
        if (!is_dir($dir)) {
            throw new InvalidArgument('Impossible to find directory '.$dir);
        }

        if ($recursive === self::NO_RECURSIVE) {

            $iterator = new \FilesystemIterator($dir);
            foreach ($iterator as $file) {
                if (is_file($iterator->current())) {
                    require_once $iterator->current();
                }
            }

        } else {

            $iterator = new \RecursiveDirectoryIterator($dir);
            foreach(new \RecursiveIteratorIterator($iterator) as $file) {
                if (is_file($file->getPathname())) {
                    echo $file->getPathname().'<br>';
                    require_once $file->getPathname();
                }
            }
            
        }

    }
    
    public function loadFile($filePath)
    {
        if (is_file($filePath)) {
            require_once $filePath;
        } else {
            throw new InvalidArgument('Impossible to find file located at '.$filePath);
        }
    }

    public function loadController($controller, $module = 'default')
    {
        $path = ROOT.DS.'modules'.DS.$module.DS.'controllers'.DS.$controller.'.php';
        if (is_file($path)) {
            require_once $path;
        } else {
            throw new InvalidArgument('Impossible to find controller class file located at '.$path.'.');
        }
    }
}

?>