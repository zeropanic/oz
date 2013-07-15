<?php

namespace Oz\Traits;

use /* Dépendences */
	\Oz\Ini,
	\Oz\Exception\InvalidArgument;

trait ConfigReader
{
	protected $config;

	protected function parseConfig($configFilePath, $absolutePath = false)
	{
		if (!$absolutePath)
		{
			$configFilePath = OZ_ROOT.DS.$configFilePath;
		}

        if (!is_file($configFilePath))
        {
            throw new InvalidArgument('Impossible to find configuration file in '.OZ_ROOT.DS.$configFilePath);
        }
        
        $iniOptions = array('process.sections' => true,
                            'section'         => OZ_APP_ENV,
                            'returnFormat'    => Ini::RETURN_OBJECT);
        return Ini::parse($configFilePath, $iniOptions);
	}

	public function setConfig(Ini $config)
	{
		$this->config = $config;
	}

	public function getConfig()
	{
		return $this->config;
	}

}