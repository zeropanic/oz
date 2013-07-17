<?php

/**
 * This interface describes what it needs to build a correct Response class
 * @package  Response
 * @author   zeropanic <zeropanic@myself.com>
 * @version  1.0
 * @namespace Oz\Http\Response
 */

namespace Oz\Http\Response;

interface ResponseInterface
{

	public function redirect($location);

	public function redirect404();

	public function redirect501();

	public function setBody($body);

	public function getBody();

	public function send();

}

?>