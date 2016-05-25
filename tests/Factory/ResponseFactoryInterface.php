<?php

namespace UMA\Tests\Psr\Http\Message\Factory;

use Psr\Http\Message\ResponseInterface;

interface ResponseFactoryInterface
{
    /**
     * @param int         $statusCode
     * @param string[]    $headers
     * @param string|null $body
     *
     * @return ResponseInterface
     */
    public function createResponse($statusCode, array $headers = [], $body = null);

    /**
     * @return string
     */
    public function responseType();
}
