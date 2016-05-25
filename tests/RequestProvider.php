<?php

namespace UMA\Tests\Psr\Http\Message;

use Psr\Http\Message\RequestInterface;
use UMA\Tests\Psr\Http\Message\Factory\RequestFactoryInterface;

class RequestProvider
{
    /**
     * @var RequestFactoryInterface[]
     */
    private $factories = [];

    /**
     * @param RequestFactoryInterface $factory
     *
     * @return RequestProvider
     */
    public function addFactory(RequestFactoryInterface $factory)
    {
        $this->factories[$factory->requestType()] = $factory;

        return $this;
    }

    /**
     * @param string      $type
     * @param string      $method
     * @param string      $url
     * @param string[]    $headers
     * @param string|null $body
     *
     * @return RequestInterface
     */
    public function createRequest($type, $method, $url, array $headers = [], $body = null)
    {
        return $this->factories[$type]
            ->createRequest($method, $url, $headers, $body);
    }

    /**
     * @param string      $method
     * @param string      $url
     * @param string[]    $headers
     * @param string|null $body
     *
     * @return RequestInterface[]
     */
    public function shotgun($method, $url, array $headers = [], $body = null)
    {
        $requests = [];

        foreach ($this->factories as $factory) {
            $requests[] = $factory->createRequest($method, $url, $headers, $body);
        }

        return $requests;
    }
}
