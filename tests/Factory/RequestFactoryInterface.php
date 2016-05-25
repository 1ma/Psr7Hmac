<?php

namespace UMA\Tests\Psr\Http\Message\Factory;

use Psr\Http\Message\RequestInterface;

interface RequestFactoryInterface
{
    /**
     * @param string      $method
     * @param string      $url
     * @param string[]    $headers
     * @param string|null $body
     *
     * @return RequestInterface
     */
    public function createRequest($method, $url, array $headers = [], $body = null);

    /**
     * @return string
     */
    public function requestType();
}
