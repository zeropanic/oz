<?php

/**
 * This class extends \Oz\Config and allows you build a config object
 * from an ini file. It can process a nesting through your ini files,
 * process a section (usually it should be the app environment), and
 * by heritance of \Oz\Config whom implements \ArrayAccess the instance
 * can be used as an array (e.i: $object['attribute']) or be looped
 * with foreach.
 * @package  Config
 * @subpackage Ini
 * @author  zeropanic <zeropanic@myself.com>
 * @version  1.0
 * @namespace Oz\Config
 * @uses  \Oz\Config parent class
 * @uses  \Oz\Config\Ini\Exception\InvalidArgument exception to throw
 *        if the class cant reach the supplied file.
 * @extends \Oz\Config
 */

namespace Oz\Config;

use 
	/* Static denpendencies */
	\Oz\Config,
	/* Exception */
	\Oz\Config\Ini\Exception\InvalidArgument;

class Ini extends Config
{
	/**
	 * class constructor has 2 optionals parameters. By providing em,
	 * you ask the class to parse the first parameter with the given
	 * options in the second parameter.
	 * @access  public
	 * @param mixed  $iniFilePath optional: (string) path to the ini file
	 *                            you want to parse, or null if you wont
	 *                            to parse it but only populate an already
	 *                            existing instance.
	 * @param array  $options     optional: the options for parsing options
	 *                            are indexes and only 4 are availables for
	 *                            ini config files:
	 *                            'process.sections' (bool), 'section'
	 *                            (string) name of the section to process
	 *                            if 'process.section' === true,
	 *                            'process.nesting' (bool) if you want to
	 *                            process a nesting and 'nesting.separator'
	 *                            (string) the separator to use for nest the
	 *                            result.
	 * @throws  \Oz\Config\Ini\Exception\InvalidArgument\ If the class cannot
	 *          find the specified $iniFilePath, if one is specified.
	 * @return  void
	 */
	public function __construct($iniFilePath = null, array $options = array())
	{
		if (is_string($iniFilePath))
		{
			if (!is_file($iniFilePath)) {
				throw new InvalidArgument(
					'Impossible to find the specified file '.$iniFilePath
				);
			}

			if (isset($options['process.sections'])
				&& $options['process.sections'] === true)
			{
				$iniArray = parse_ini_file($iniFilePath, true);

			} else {
				$iniArray = parse_ini_file($iniFilePath);
			}

			parent::__construct($iniArray, $options);
		}
	}

}

?>