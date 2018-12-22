<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Factory;

use Kambo\Http\Message\Request;
use Kambo\Http\Message\Stream;
use Kambo\Http\Message\Uri;
use Psr\Http\Message\RequestInterface;

final class KamboFactory implements FactoryInterface
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

        $query = $parseUrl['query'] ?? null;

        $uri = new Uri($parseUrl['scheme'], $parseUrl['host'], null, $parseUrl['path'], $query);

        // Kambo expects multi-valued headers to be passed as a comma-separated string
        foreach ($headers as $name => $value) {
            if (is_array($value)) {
                $headers[$name] = implode(',', $value);
            }
        }

        return new Request($method, $uri, $headers, new Stream(self::stream($body)));
    }

    /**
     * {@inheritdoc}
     */
    public static function requestClass(): string
    {
        return Request::class;
    }
}
