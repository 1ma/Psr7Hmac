<?php

namespace UMA\Psr7Hmac\Internal;

use Psr\Http\Message\RequestInterface;

final class RequestSerializer
{
    const CRLF = "\r\n";

    /**
     * Returns the string representation of an HTTP request.
     *
     * @param RequestInterface $request Request to convert to a string.
     *
     * @return string
     */
    public static function serialize(RequestInterface $request)
    {
        return self::requestLine($request).self::headers($request).$request->getBody();
    }

    private static function requestLine(RequestInterface $request)
    {
        $method = $request->getMethod();
        $target = self::fixTarget(trim($request->getRequestTarget()));
        $protocol = 'HTTP/'.$request->getProtocolVersion();

        return "$method $target $protocol".self::CRLF;
    }

    private static function headers(RequestInterface $request)
    {
        $headers = $request->getHeaders();

        unset($headers['host']);
        unset($headers['Host']);

        $headerLines = [];
        foreach ($headers as $name => $value) {
            $value = is_array($value) ? $value : [$value];
            $normalizedName = (new HeaderNameNormalizer())->normalize($name);

            $headerLines[$normalizedName] = $normalizedName.': '.implode(',', $value).self::CRLF;
        }

        ksort($headerLines);

        $host = $request->getUri()->getHost();

        return "host: $host".self::CRLF.implode($headerLines).self::CRLF;
    }

    private static function fixTarget($target)
    {
        if (!array_key_exists('query', $parsedTarget = parse_url($target))) {
            return $target;
        }

        parse_str($parsedTarget['query'], $query);

        ksort($query);

        return $parsedTarget['path'].'?'.http_build_query($query);
    }
}
