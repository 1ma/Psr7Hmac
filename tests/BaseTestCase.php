<?php

namespace UMA\Tests\Psr\Http\Message;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $method
     * @param string $url
     *
     * @return RequestInterface[]
     */
    protected function psr7RequestShotgun($method, $url)
    {
        $parsedUrl = parse_url($url);

        return [
            new \Asika\Http\Request($url, $method),
            new \GuzzleHttp\Psr7\Request($method, $url),
            new \Phyrexia\Http\Request($method, $url),
            new \RingCentral\Psr7\Request($method, $url),
            new \Slim\Http\Request(
                $method,
                new \Slim\Http\Uri($parsedUrl['scheme'], $parsedUrl['host'], null, $parsedUrl['path']),
                new \Slim\Http\Headers(),
                [],
                [],
                new \Slim\Http\RequestBody()),
            new \Wandu\Http\Psr\Request('GET', new \Wandu\Http\Psr\Uri($url)),
            new \Zend\Diactoros\Request($url, $method),
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
