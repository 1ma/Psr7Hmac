<?php

namespace UMA\Tests\Psr\Http\Message;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $method
     * @param string $url
     * @param string[] $headers
     *
     * @return RequestInterface[]
     */
    protected function psr7RequestShotgun($method, $url, array $headers = [])
    {
        $parsedUrl = parse_url($url);

        return [
            new \Asika\Http\Request($url, $method, 'php://memory', $headers),
            new \GuzzleHttp\Psr7\Request($method, $url, $headers),
            new \Phyrexia\Http\Request($method, $url, $headers),
            new \RingCentral\Psr7\Request($method, $url, $headers),
            new \Slim\Http\Request(
                $method,
                new \Slim\Http\Uri($parsedUrl['scheme'], $parsedUrl['host'], null, $parsedUrl['path']),
                new \Slim\Http\Headers($headers),
                [],
                [],
                new \Slim\Http\RequestBody()),
            new \Zend\Diactoros\Request($url, $method, 'php://temp', $headers),
            new \Wandu\Http\Psr\Request('GET', new \Wandu\Http\Psr\Uri($url), '1.1', $headers),
        ];
    }

    /**
     * @param int      $status
     * @param string[] $headers
     *
     * @return ResponseInterface[]
     */
    protected function psr7ResponseShotgun($status, array $headers = [])
    {
        return [
            new \Asika\Http\Response('php://memory', $status, $headers),
            new \GuzzleHttp\Psr7\Response($status, $headers),
            new \Phyrexia\Http\Response($status, $headers),
            new \RingCentral\Psr7\Response($status, $headers),
            new \Slim\Http\Response($status, new \Slim\Http\Headers($headers)),
            new \Wandu\Http\Psr\Response($status, '', '1.1', $headers),
            new \Zend\Diactoros\Response('php://memory', $status, $headers),
        ];
    }
}
