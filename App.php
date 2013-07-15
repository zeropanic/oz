<?php

namespace Oz;

/**
 * @author ZeRo <zeropanic@gmail.com>
 * @version: 1.0
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License
 */


use /* Dépendences */
    \Oz\Di\Dic,
    \Oz\App\AppInterface,
    \Oz\Traits\ConfigReader,
    /* Exceptions */
    \Oz\Exception as OzException,
    \Oz\App\Exception,
    \Oz\App\Exception\InvalidArgument;


class App implements AppInterface
{
    use ConfigReader;

    protected $paths = array();

    /**
     * Constructeur
     * @type: private.
     * @but: initialiser les ressources primaires avec leurs configurations.
     * @return: void.
     */
    public function __construct($configPath)
    {
        $this->config = $this->parseConfig($configPath);

        $this->hydrate();
    }

    protected function hydrate()
    {
        $config = $this->getConfig();

        if (isset($config->app->path) && $config->app->path instanceof Ini) {
            foreach ($config->app->path as $key => $value){
                $this->setPath($key, $value);
            }
        }

        if (isset($config->app->constant) && $config->app->constant instanceof Ini) {
            foreach ($config->app->constant as $constant => $value){
                define($constant, $value);
            }
        }

        unset($config);
    }

    protected function isValidConfig()
    {
        if (isset($this->config->app->default)
            && isset($this->config->app->path)
            && isset($this->config->app->config)) {
            return true;
        } else {
            return false;
        }
    }

    public function init()
    {
        return $this;
    }

    /**
     * @type: public.
     * @but: lancer l'application avec toutes ses phases.
     * @return: void.
     */
    public function run()
    {
        try {
            Dic::getInstance()->getService('dispatcher')->dispatch();
        } catch(OzException $e) {
            echo $e;
        }
    }

    public function setPath($pathKey, $path)
    {
        $this->paths[$pathKey] = $path;
    }

    public function getPath($pathKey)
    {
        return array_key_exists($pathKey, $this->paths) ? $this->paths[$pathKey] : false;
    }
}

?>
