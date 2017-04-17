<?php

namespace UMA\Tests\Psr7Hmac\Factory;

use Slim\Http\Body;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Uri;

class SlimFactory implements FactoryInterface
{
    use StreamHelper;

    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public static function request($method, $url, array $headers = [], $body = null)
    {
        $parseUrl = parse_url($url);

        $query = array_key_exists('query', $parseUrl) ?
            $parseUrl['query'] : null;

        $uri = new Uri($parseUrl['scheme'], $parseUrl['host'], null, $parseUrl['path'], $query);

        return new Request($method, $uri, new Headers($headers), [], [], new Body(self::stream($body)));
    }

    /**
     * {@inheritdoc}
     */
    public static function requestClass()
    {
        return Request::class;
    }
}
