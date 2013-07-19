<?php

/**
 * This class is intended to read configuration through arrays. This is
 * a base for derived class providing a nesting process with a specific
 * separator.
 * @package  Config
 * @author  zeropanic <zeropanic@myself.com>
 * @version  1.0
 * @namespace Oz
 * @implements \ArrayAccess
 * @uses  \Oz\Traits\ArrayAccess the trait for easily implements \ArrayAcess
 */

namespace Oz;

use
	/* Static dependency */
	\Oz\Traits\ArrayAccess;

class Config implements \ArrayAccess
{
	/**
	 * the ArrayAccess trait imported above.
	 */
	use ArrayAccess;

	/**
	 * class constructor can be used to parse an array with or without
	 * options with both are specified. It can also do nothing, useful
	 * if you want to populate the object with populateFromArraybmethod.
	 * @access public
	 * @param mixed  $config  optional: the array you want to parse
	 * @param array  $options optional: the options for parsing options
     *                        are indexes and only 4 are availables for
     *                        array config files: 'process.sections' (bool)
     *                        'section' (string) name of the section to
     *                        process, 'process.nesting' (bool) to process
     *                        a nesting recursively, 'nesting.separator'
     *                        to use a custom separator during nesting.                             
	 * @return  void
	 */
	public function __construct($config = null, array $options = array())
	{
		if (!is_null($config) && is_array($config)) {

            if (isset($options['process.sections'])
                && $options['process.sections'] === true) {
                $config = $this->processSections($config);

                $section = isset($options['section']) ?
                    $options['section'] : OZ_APP_ENV;

                if (isset($config[$section])) {
                    $config = $config[$section];
                }   
            }

			if (isset($options['process.nesting'])
				&& $options['process.nesting'] === true) {

				$separator = isset($options['nesting.separator']) ?
					$options['nesting.separator'] : '.'; 

				$config = $this->processRecursiveNesting($config, $separator);
			}

            var_dump($config); exit;
            $this->populateFromArray($config, $this);
		}
	}

	/**
	 * processRecursiveNesting allows you to process nesting in an array in a
	 * recursive way.
	 * @access  protected
	 * @param  array  $array     the array you want to process nesting
	 * @param  string $separator the separator for proccessing nesting
	 * @return array             the final array after nested all nodes
	 */
	protected function processRecursiveNesting(array $array, $separator)
	{
		$returnArray = array();

    	if (is_array($array)) {

        	foreach ($array as $key => $value) {
            	if (is_array($value)) {
                	$array[$key] = $this->processRecursiveNesting($value, $separator);
            	}

            	$x = explode($separator, $key);
            	
            	if (!empty($x[1])) {
                	$x = array_reverse($x, true);
                	if (isset($returnArray[$key])) {
                    	unset($returnArray[$key]);
                	}

                	if (!isset($returnArray[$x[0]])) {
                    	$returnArray[$x[0]] = array();
                	}
                	$first = true;
                	foreach ($x as $k => $v) {
                    	if ($first === true) {
                        	$b = $array[$key];
                        	$first = false;
                    	}
                    	$b = array($v => $b);
                	}
                	$returnArray[$x[0]] = array_merge_recursive($returnArray[$x[0]], $b[$x[0]]);
            	} else {
                	$returnArray[$key] = $array[$key];
            	}
        	}
    	}

    	return $returnArray;
    }

    /**
     * processSections map the array and fetch defined sections, it can
     * also set a 'section heritance' by using sections such as [dev : prod]
     * whereas dev will herit datas from prod and will override all
     * settings if they already are in prod.
     * @param  array  $array [description]
     * @return [type]        [description]
     */
    protected function processSections(array $array)
    {
        $returnArray = array();

        foreach ($array as $key => $value) {
            $sections = explode(':', $key);

            if (!empty($sections[1])) {
                $sectionsArray = array();
                
                foreach ($sections as $sectionKey => $sectionValue) {
                    $sectionsArray[$sectionKey] = trim($sectionValue);
                }
                
                $sectionsArray = array_reverse($sectionsArray, true);
                
                foreach ($sectionsArray as $k => $v) {
                    $c = $sectionsArray[0];
                    
                    if (empty($returnArray[$c])) {
                        $returnArray[$c] = array();
                    }
                    if (isset($returnArray[$sectionsArray[1]])){
                        $returnArray[$c] = array_merge($returnArray[$c], $returnArray[$sectionsArray[1]]);
                    }
                    if ($k === 0){
                        $returnArray[$c] = array_merge($returnArray[$c], $array[$key]);
                    }
                }
            } else {
                $returnArray[$key] = $array[$key];
            }
        }


        return $returnArray;
    }

    /**
     * populateFromArray recursively populate the object passed as 
     * second parameter with the array passed as first parameter
     * it creates object from an array node instancing the class 
     * using this method (new self())
     * @param  array  $array  array to transforms recursively
     * @param  object $object object to populate recursively
     * @return void
     */
	protected function populateFromArray(array $array, $object)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
            	$class = get_class($object);
            	$object->{$key} = new $class();
            	$this->populateFromArray($value, $object->{$key});
            } else {
            	$object->{$key} = $value;
            }
        }
    }

}

?>