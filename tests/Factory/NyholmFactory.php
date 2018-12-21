<?php

namespace UMA\Tests\Psr7Hmac\Factory;

use Nyholm\Psr7\Request as NyholmRequest;

class NyholmFactory implements FactoryInterface
{

    /**
     * {@inheritdoc}
     *
     * @return NyholmRequest
     */
    public static function request($method, $url, array $headers = [], $body = null)
    {
        return new NyholmRequest($method, $url, $headers, $body, '1.1');
    }

    /**
     * {@inheritdoc}
     */
    public static function requestClass()
    {
        return NyholmRequest::class;
    }
}
