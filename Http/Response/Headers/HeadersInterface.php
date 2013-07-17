<?php

/**
 * This interface describes what it needs to build a correct Headers class
 * @package  Response
 * @subpackage  Headers
 * @author   zeropanic <zeropanic@myself.com>
 * @version  1.0
 * @namespace Oz\Http\Response\Headers
 */

namespace Oz\Http\Response\Headers;

interface HeadersInterface
{
	/**
	 * @const REMOVE_ALL specify this constant as parameter to removeHeader
	 * method for removing all defined headers
	 */
	const REMOVE_ALL = 1;

	/**
	 * @const DONT_REPLACE specify this constant as third parameter to
	 * setHeader method and it will not replace the supplied directive
	 * if the directive is already defined
	 */
	const DONT_REPLACE = 2;

	public function setStatusCode($code);

	public function getStatusCode();

	public function setHeader($directive, $value, $replace);

	public function getHeader($directive);

	public function setHeaders(array $headers);

	public function getHeaders();

	public function hasHeader($directive);

	public function removeHeader($header);

	public function sendHeaders();
}

?>