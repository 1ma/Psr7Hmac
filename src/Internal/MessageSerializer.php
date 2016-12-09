<?php

namespace UMA\Psr7Hmac\Internal;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class MessageSerializer
{
    const SP = ' ';
    const CRLF = "\r\n";

    /**
     * Returns the string representation of an HTTP message.
     *
     * @param MessageInterface $message Message to convert to a string.
     *
     * @return string
     *
     * @throws \InvalidArgumentException When $message is neither an implementation
     *                                   of RequestInterface nor ResponseInterface.
     */
    public static function serialize(MessageInterface $message)
    {
        return self::startLine($message).self::headers($message).self::CRLF.$message->getBody();
    }

    private static function startLine(MessageInterface $message)
    {
        if ($message instanceof RequestInterface) {
            return self::requestLine($message);
        } elseif ($message instanceof ResponseInterface) {
            return self::statusLine($message);
        } else {
            throw new \InvalidArgumentException('Unknown message type');
        }
    }

    private static function headers(MessageInterface $message)
    {
        $headers = $message->getHeaders();

        unset($headers['host']);
        unset($headers['Host']);

        $headerLines = [];
        foreach ($headers as $name => $value) {
            $value = is_array($value) ? $value : [$value];
            $normalizedName = (new HeaderNameNormalizer())->normalize($name);

            $headerLines[$normalizedName] = $normalizedName.':'.self::SP.implode(',', $value).self::CRLF;
        }

        ksort($headerLines);

        return implode($headerLines);
    }

    private static function requestLine(RequestInterface $request)
    {
        $method = $request->getMethod();
        $target = self::fixTarget(trim($request->getRequestTarget()));
        $protocol = 'HTTP/'.$request->getProtocolVersion();
        $host = $request->getUri()->getHost();

        return $method.self::SP.$target.self::SP.$protocol.self::CRLF."host: $host".self::CRLF;
    }

    private static function statusLine(ResponseInterface $response)
    {
        $protocol = 'HTTP/'.$response->getProtocolVersion();
        $statusCode = $response->getStatusCode();
        $reasonPhrase = $response->getReasonPhrase();

        return $protocol.self::SP.$statusCode.self::SP.$reasonPhrase.self::CRLF;
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
