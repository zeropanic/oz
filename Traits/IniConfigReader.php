<?php

/**
 * This Trait allows you to read and store a config ini file at Oz's style.
 * Its very useful coupled with the Dependency Injection because it handles
 * absolute or relative (OZ_ROOT directory) paths to your configuration files.
 * @package  Traits
 * @author  zeropanic <zeropanic@myself.com>
 * @version  1.0
 * @namespace Oz\Traits
 * @uses  \Oz\Config\Ini our ini file reader
 * @uses  \Oz\Config\Ini\Exception\InvalidArgument exception to throw if the
 *        doesnt exists
 */

namespace Oz\Traits;

use
	/* Static dependency */
	\Oz\Config\Ini,
	/* Exception */
	\Oz\Config\Ini\Exception\InvalidArgument;

trait IniConfigReader
{
	/**
	 * $config will store the result of the parsing
	 * @var \Oz\Ini
	 * @access  protected
	 */
	protected $config;

	/**
	 * parseIniConfig parse the ini file with Oz's style.
	 * @param  string  $configFilePath path to the ini file
	 * @param  boolean $absolutePath   true if $configFilePath is absolute
	 *                                 false if not.
	 * @access  protected
	 * @throws \Oz\Exception\InvalidArgument If the file doesn't exists. 
	 * @return \Oz\Ini 				   the ini instance of the parsed file
	 */
	protected function parseIniConfig($configFilePath, $absolutePath = true)
	{
		if ($absolutePath) {
			$configFilePath = OZ_ROOT.DS.$configFilePath;
		}

        if (!is_file($configFilePath)) {
            throw new InvalidArgument('Impossible to find configuration file in '.OZ_ROOT.DS.$configFilePath);
        }
        
        $iniOptions = array(
			'process.sections'  => true,
			'section'           => OZ_APP_ENV,
			'process.nesting'   => true,
			'nesting.separator' => '.'
        );

        return new Ini($configFilePath, $iniOptions);
	}

	/**
	 * setIniConfig is the setter of $config
	 * @param \Oz\Ini $config Ini instance you want to set as config
	 * @access  public
	 * @return  void
	 */
	public function setIniConfig(Ini $config)
	{
		$this->config = $config;
	}

	/**
	 * getIniConfig is the getter for $config
	 * @access  public
	 * @return \Oz\Ini the instance of the parsed config file
	 */
	public function getIniConfig()
	{
		return $this->config;
	}

}