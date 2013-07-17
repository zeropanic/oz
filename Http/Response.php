<?php

/**
 * Response is designed to handle the Http Response by redirecting,
 * redirecting to 404/501, getting/setting Response's body and sending
 * Headers & Response's body.
 * @package  Response
 * @author   zeropanic <zeropanic@myself.com>
 * @version  1.0
 * @namespace Oz\Http
 * @implements \Oz\Http\Response\ResponseInterface
 * @uses  \Oz\Http\Response\ResponseInterface the interface to implements
 *        if we want to build a correct response class
 * @uses  \Oz\Http\Response\Headers\HeadersInterface the interface for
 *        headers dependency injection
 * @uses  \Oz\Http\Response\Exception the Response's package's main
 *        exception class
 * @uses  \Oz\Http\Response\Exception\InvalidArgument the InvalidArgument
 *        Exception class for the Response package
 */

namespace Oz\Http;

use /* Static dependencies */
    \Oz\Http\Response\ResponseInterface,
    \Oz\Http\Response\Headers\HeadersInterface;

class Response implements ResponseInterface
{
    /**
     * $headers store the headers instance
     * @access  protected
     * @var \Oz\Http\Response\headers\HeadersInterface
     */
    protected $headers;

    /**
     * $body store the Reponse's body
     * @access  protected
     * @var string
     */
    protected $body;

    /**
     * class constructor starts output buffering and set the headers
     * instance
     * @access  public
     * @param \Oz\Http\Response\Headers\HeadersInterface $headers headers
     *                                                   sinstance
     * @return  void
     */
    public function __construct(HeadersInterface $headers)
    {
        \ob_start();
        $this->headers = $headers;
    }

    /**
     * redirect to a specific location (based on the OZ_BASE_URL constant)
     * @access  public
     * @param  string $location location to redirect
     * @return void
     */
    public function redirect($location)
    {
        header('Location: '.OZ_BASE_URL.'/'.$location);
        exit;
    }

    /**
     * redirects to 404 error page
     * @access  public
     * @return void
     */
    public function redirect404()
    {
        header('Location: '.OZ_BASE_URL.'/error/notfound');
        exit;
    }

    /**
     * redirects to 501 error page
     * @access  public
     * @return void
     */
    public function redirect501()
    {
        header('Location: '.OZ_BASE_URL.'/error/deniedaccess');
        exit;
    }

    /**
     * setBody is the setter for the $body attribute
     * @access  public
     * @param string $body body of the Response
     * @return  void
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * getBody is the getter for $body attribute
     * @access  public
     * @return string $body attribute
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * calls the headers and send them to the client with the body response
     * @access  public
     * @return void
     */
    public function send()
    {
        $this->setBody(ob_get_clean());

        $this->headers->sendHeaders();

        echo $this->body;
    }
}