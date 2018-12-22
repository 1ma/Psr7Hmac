<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Factory;

use Psr\Http\Message\RequestInterface;
use Slim\Http\Body;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Uri;

final class SlimFactory implements FactoryInterface
{
    use StreamHelper;

    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public static function request(string $method, string $url, array $headers = [], string $body = null): RequestInterface
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
    public static function requestClass(): string
    {
        return Request::class;
    }
}
