<?php

namespace UMA\Tests;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HMACTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $method
     * @param string $uri
     *
     * @return RequestInterface[]
     */
    protected function psr7RequestShotgun($method, $uri)
    {
        $parseUrl = parse_url($uri);
        $scheme = $parseUrl['scheme'];
        $host = $parseUrl['host'];
        $path = $parseUrl['path'];

        return [
            new \Asika\Http\Request($uri, $method),
            new \GuzzleHttp\Psr7\Request($method, $uri),
            new \Phyrexia\Http\Request($method, $uri),
            new \RingCentral\Psr7\Request($method, $uri),
            new \Slim\Http\Request(
                $method,
                new \Slim\Http\Uri($scheme, $host, null, $path),
                new \Slim\Http\Headers(),
                [],
                [],
                new \Slim\Http\RequestBody()),
            new \Wandu\Http\Psr\Request('GET', new \Wandu\Http\Psr\Uri($uri)),
            new \Zend\Diactoros\Request($uri, $method),
        ];
    }

    /**
     * @param int $status
     *
     * @return ResponseInterface[]
     */
    protected function psr7ResponseShotgun($status)
    {
        return [
            new \Asika\Http\Response('php://memory', $status),
            new \GuzzleHttp\Psr7\Response($status),
            new \Phyrexia\Http\Response($status),
            new \RingCentral\Psr7\Response($status),
            new \Slim\Http\Response($status),
            new \Wandu\Http\Psr\Response($status),
            new \Zend\Diactoros\Response('php://memory', $status),
        ];
    }
}
