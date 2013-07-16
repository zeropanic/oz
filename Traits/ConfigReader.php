<?php

/**
 * This Trait allows you to read and store a config ini file at Oz's style.
 * Its very useful coupled with the Dependency Injection because it handles
 * absolute or relative (OZ_ROOT directory) paths to your configuration files.
 * @package  Traits
 * @author  zeropanic <zeropanic@myself.com>
 * @version  1.0
 * @namespace Oz\Traits
 * @uses  \oz\Ini our ini file reader
 * @uses  \Oz\Exception\InvalidArgument exception to throw if the 
 */

namespace Oz\Traits;

use
	/* Static dependency */
	\Oz\Ini,
	/* Exception */
	\Oz\Exception\InvalidArgument;

trait ConfigReader
{
	/**
	 * $config will store the result of the parsing
	 * @var \Oz\Ini
	 */
	protected $config;

	/**
	 * parseConfig parse the ini file with Oz's style.
	 * @param  string  $configFilePath path to the ini file
	 * @param  boolean $absolutePath   true if $configFilePath is absolute
	 *                                 false if not.
	 * @throws \Oz\Exception\InvalidArgument If the file doesn't exists. 
	 * @return \Oz\Ini 				   the ini instance of the parsed file
	 */
	protected function parseConfig($configFilePath, $absolutePath = false)
	{
		if (!$absolutePath) {
			$configFilePath = OZ_ROOT.DS.$configFilePath;
		}

        if (!is_file($configFilePath)) {
            throw new InvalidArgument('Impossible to find configuration file in '.OZ_ROOT.DS.$configFilePath);
        }
        
        $iniOptions = array('process.sections' => true,
                            'section'         => OZ_APP_ENV,
                            'returnFormat'    => Ini::RETURN_OBJECT);

        return Ini::parse($configFilePath, $iniOptions);
	}

	/**
	 * setConfig is the setter of $config
	 * @param \Oz\Ini $config Ini instance you want to set as config
	 */
	public function setConfig(Ini $config)
	{
		$this->config = $config;
	}

	/**
	 * getConfig is the getter for $config
	 * @return \Oz\Ini the instance of the parsed config file
	 */
	public function getConfig()
	{
		return $this->config;
	}

}