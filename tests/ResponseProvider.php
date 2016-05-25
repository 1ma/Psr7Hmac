<?php

namespace UMA\Tests\Psr\Http\Message;

use Psr\Http\Message\ResponseInterface;
use UMA\Tests\Psr\Http\Message\Factory\ResponseFactoryInterface;

class ResponseProvider
{
    /**
     * @var ResponseFactoryInterface[]
     */
    private $factories = [];

    /**
     * @param ResponseFactoryInterface $factory
     *
     * @return ResponseProvider
     */
    public function addFactory(ResponseFactoryInterface $factory)
    {
        $this->factories[$factory->responseType()] = $factory;

        return $this;
    }

    /**
     * @param string      $type
     * @param string      $statusCode
     * @param string[]    $headers
     * @param string|null $body
     *
     * @return ResponseInterface
     */
    public function createResponse($type, $statusCode, array $headers = [], $body = null)
    {
        return $this->factories[$type]
            ->createResponse($statusCode, $headers, $body);
    }

    /**
     * @param string      $statusCode
     * @param string[]    $headers
     * @param string|null $body
     *
     * @return ResponseInterface[]
     */
    public function shotgun($statusCode, array $headers = [], $body = null)
    {
        $responses = [];

        foreach ($this->factories as $factory) {
            $responses[] = $factory->createResponse($statusCode, $headers, $body);
        }

        return $responses;
    }
}
