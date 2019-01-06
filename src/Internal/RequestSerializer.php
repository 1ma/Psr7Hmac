<?php

declare(strict_types=1);

namespace UMA\Psr7Hmac\Internal;

use Psr\Http\Message\RequestInterface;

final class RequestSerializer
{
    private const CRLF = "\r\n";

    /**
     * Returns the string representation of an HTTP request.
     *
     * @param RequestInterface $request Request to convert to a string.
     *
     * @return string
     */
    public static function serialize(RequestInterface $request): string
    {
        return self::requestLine($request).self::headers($request).$request->getBody();
    }

    private static function requestLine(RequestInterface $request): string
    {
        $method = $request->getMethod();
        $target = self::fixTarget(\trim($request->getRequestTarget()));
        $protocol = 'HTTP/'.$request->getProtocolVersion();

        return "$method $target $protocol".self::CRLF;
    }

    private static function headers(RequestInterface $request): string
    {
        $headers = $request->getHeaders();

        unset($headers['host'], $headers['Host'], $headers['HTTP_HOST']);

        $headerLines = [];
        foreach ($headers as $name => $value) {
            $value = \is_array($value) ? $value : [$value];
            $normalizedName = HeaderNameNormalizer::normalize($name);

            $headerLines[$normalizedName] = $normalizedName.': '.\implode(',', $value).self::CRLF;
        }

        \ksort($headerLines, SORT_STRING);

        $host = $request->getUri()->getHost();

        return "host: $host".self::CRLF.\implode($headerLines).self::CRLF;
    }

    private static function fixTarget($target): string
    {
        if (!\array_key_exists('query', $parsedTarget = \parse_url($target))) {
            return $target;
        }

        \parse_str($parsedTarget['query'], $query);

        \ksort($query, SORT_STRING);

        return $parsedTarget['path'].'?'.\http_build_query($query);
    }
}
