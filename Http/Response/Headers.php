<?php

/**
 * Headers manages Response headers by adding / removing or testing the
 * headers. It can set header with chaining methods or with a bigger array.
 * it also manages the response status code.
 * @package  Response
 * @subpackage  Headers
 * @author   zeropanic <zeropanic@myself.com>
 * @version  1.0
 * @namespace Oz\Http\Response
 * @implements \Oz\Http\Response\Headers\HeadersInterface
 * @uses  \Oz\Traits\IniConfigReader the ini config file reader
 * @uses  \Oz\Http\Response\Headers\HeadersInterface the interface to
 *        implements to build a correct Headers class.
 * @uses  \Oz\Http\Response\Headers\Exception the main exception class of
 *        the Headers subpackage
 * @uses  \Oz\Http\Response\Headers\Exception\InvalidArgument the
 *        InvalidArgument Exception class of the Headers subpackage
 */

namespace Oz\Http\Response;

use
    /* Static dependencies */
    \Oz\Config\Ini,
    \Oz\Traits\IniConfigReader,
    \Oz\Http\Response\Headers\HeadersInterface,
    /* Exceptions */
    \Oz\Http\Response\Headers\Exception,
    \Oz\Http\Response\Headers\Exception\InvalidArgument;

class Headers implements HeadersInterface
{
    /**
     * @uses  \Oz\Traits\ConfigReader the trait for reading config files
     */
    use IniConfigReader;

    /**
     * $headers is an array containing all defined headers.
     * @access  protected
     * @var array
     */
    protected $headers = array();

    /**
     * class constructor parse the config file and sets the default
     * headers specified in the config file.
     * @access  public
     * @param string $configFilePath path to the config file
     * @return  void
     */
    public function __construct($configFilePath)
    {
    	$this->setIniConfig($this->parseIniConfig($configFilePath));

        if (isset($this->config->headers->default) && $this->config->headers->default instanceof Ini) {
            foreach ($this->config->headers->default as $directive => $value) {
                $this->setHeader($directive, $value);
            }
        }
    }

    /**
     * setStatusCode allows you to set an Http Status code
     * @access  public
     * @param int $statusCode the status code number (200, 404, 501 etc)
     * @return  void
     */
    public function setStatusCode($statusCode)
    {
        \http_response_code($statusCode);
    }

    /**
     * getStatusCode allows you to retrieve the status code
     * @access  public
     * @return int the status code number (200, 404, 501 etc)
     */
    public function getStatusCode()
    {
        return \http_response_code();
    }

    /**
     * setHeader allows you to define a header, it replaces an already
     * defined headers by default but you can change this by supplying
     * HeadersInterface::DONT_REPLACE constant as third parameter.
     * @access  public
     * @param string $directive the directive of your header, for example
     *                          it can be Content-Type or X-Powered-By
     *                          just dont write the colon
     * @param mixed $value      value of the directive can be string, or int
     * @param boolean $replace  if specified,
     * @return  \Oz\Http\Response\Headers\HeadersInterface headers instance
     */
    public function setHeader($directive, $value, $replace = null)
    {
    	if (is_null($replace) || $replace != static::DONT_REPLACE) {
    		$this->headers[$directive] = $value;
    	} elseif ($replace == static::DONT_REPLACE) {

    		if (!$this->hasHeader($directive)) {
    			$this->headers[$directive] = $value;
    		}
    		else {
    			return $this;
    		}
    	}

        return $this;
    }

    /**
     * setHeaders allows you to set headers from a bigger
     * multidimensional array. The array must look like this:
     * array(
     *     array('directive' => 'Content-Type',
     *           'value'     => 'text/css'
     *     ),
     *     array('directive' => 'X-Powered-By',
     *           'value'     => 'PHP 5.1'
     *     ),
     * );
     * else an exception will be throw
     * @access  public
     * @param array $headers array containing headers
     * @throws  \Oz\Http\Response\Headers\InvalidArgument If the array
     *          isnt correctly built
     * @return  void
     */
    public function setHeaders(array $headers)
    {
        foreach ($headers as $directive => $value) {

            if (is_array($value)) {

                if (isset($value['directive'])) {
                    $directiveValue = $value['directive'];
                } else {
                    throw new InvalidArgument(
                        'Missing \'directive\' offset in $headers array.');
                }

                if (isset($value['value'])) {
                    $headerValue = $value['value'];
                } else {
                    throw new InvalidArgument(
                        'Missing \'value\' offset in $headers array.');
                }

                $replaceValue = isset($value['replace']) ? $value['replace'] : null;
                $this->setHeader($directiveValue, $headerValue, $replaceValue);

            } else {
                $this->setHeader($directive, $value);
            }
        }
    }

    /**
     * getHeaders is a getter returning the complete array of headers
     * @access  public
     * @return array $headers attribute
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * getHeaders allows you to get only one directive it returns null
     * if the directive wasnt defined.
     * @access  public
     * @param  string $directive name of the directive you want to retrieve
     * @return mixed valued of the directive if its exists or false if not
     */
    public function getHeader($directive)
    {
        return $this->hasHeader($directive) ? $this->headers[$directive] : false;
    }

    /**
     * hasHeader checks wether a directive was defined or not
     * @access  public
     * @param  string  $directive name of the directive you want to check
     * @return boolean            true if directive exists, false if not
     */
    public function hasHeader($directive)
    {
    	return isset($this->headers[$directive]);
    }

    /**
     * removeHeader allows you to remove one directive, or you can remove
     * all directive by supplying as first parameter the constant
     * HeadersInterface::REMOVE_ALL
     * @access  public
     * @param  mixed $directive  name of the directive you want to remove
     *                           or HeadersInterface::REMOVE_ALL if you
     *                           wish to remove all defined headers.
     * @return void
     */
    public function removeHeader($directive)
    {
        if ($directive === static::REMOVE_ALL) {
            $this->headers = array();
        } elseif (is_array($directive)) {

            foreach($directive as $key) {
            	if ($this->hasHeader($key)) {
            		unset($this->headers[$key]);
            	}
            }
        } else {

        	if (isset($this->headers[$directive])) {
        		unset($this->headers[$directive]);
        	}
        }
    }

    /**
     * sendHeaders look through $headers array attribute and set all
     * headers with their respectives directives and values.
     * @access  public
     * @return void
     */
    public function sendHeaders()
    {
    	foreach ($this->headers as $directive => $value) {
    		\header($directive.': '.$value);
    	}
    }
}

?>