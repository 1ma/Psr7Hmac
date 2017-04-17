<?php

namespace UMA\Tests\Psr7Hmac\Factory;

use Kambo\Http\Message\Request;
use Kambo\Http\Message\Stream;
use Kambo\Http\Message\Uri;

class KamboFactory
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
    public static function requestClass()
    {
        return Request::class;
    }
}
