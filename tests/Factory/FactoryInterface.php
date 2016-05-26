<?php

namespace UMA\Tests\Psr\Http\Message\Factory;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface FactoryInterface
{
    /**
     * @param string      $method
     * @param string      $url
     * @param string[]    $headers
     * @param string|null $body
     *
     * @return RequestInterface
     */
    public static function request($method, $url, array $headers = [], $body = null);

    /**
     * @param int         $statusCode
     * @param string[]    $headers
     * @param string|null $body
     *
     * @return ResponseInterface
     */
    public static function response($statusCode, array $headers = [], $body = null);

    /**
     * @return string
     */
    public static function requestClass();

    /**
     * @return string
     */
    public static function responseClass();
}
