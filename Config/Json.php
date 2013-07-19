<?php

/**
 * This class extends \Oz\Config and allows you to build a config object
 * from a json file. It can process a nesting through your json files,
 * process a section (usually it should be the app environment) with sections
 * heritance (report to \Oz\Config), and by heritance of \Oz\Config whom
 * implements \ArrayAccess the instance can be used as an array
 * (e.i: $object['attribute']) or be looped with foreach.
 * @package  Config
 * @subpackage Json
 * @author  zeropanic <zeropanic@myself.com>
 * @version  1.0
 * @namespace Oz\Config
 * @uses  \Oz\Config parent class
 * @uses  \Oz\Config\Json\Exception\InvalidArgument exception to throw
 *        if the class cant reach the supplied file.
 * @extends \Oz\Config
 */

namespace Oz\Config;

use 
	/* Static denpendencies */
	\Oz\Config,
	/* Exception */
	\Oz\Config\Json\Exception\InvalidArgument;

class Json extends Config
{
	/**
	 * class constructor has 2 optionals parameters. By providing em,
	 * you ask the class to parse the first parameter with the given
	 * options in the second parameter.
	 * @access  public
	 * @param mixed  $jsonFilePath optional: (string) path to the json file
	 *                             you want to parse, or null if you wont
	 *                             to parse it but only populate an already
	 *                             existing instance.
	 * @param array  $options      optional: the options for parsing options
	 *                             are indexes and only 4 are availables for
	 *                             json config files:
	 *                             'process.sections' (bool), 'section'
	 *                             (string) name of the section to process
	 *                             if 'process.section' === true,
	 *                             'process.nesting' (bool) if you want to
	 *                             process a nesting and 'nesting.separator'
	 *                             (string) the separator to use for nest the
	 *                             result.
	 * @throws  \Oz\Config\Json\Exception\InvalidArgument\ If the class cannot
	 *          find the specified $jsonFilePath, if one is specified.
	 * @return  void
	 */
	public function __construct($jsonFilePath = null, array $options = array())
	{
		if (is_string($jsonFilePath))
		{
			if (!is_file($jsonFilePath)) {
				throw new InvalidArgument(
					'Impossible to find the specified file '.$jsonFilePath
				);
			}

			$jsonArray = json_decode(file_get_contents($jsonFilePath), true);

			parent::__construct($jsonArray, $options);
		}
	}

}

?>