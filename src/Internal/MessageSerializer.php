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
        $headers = array_change_key_case($message->getHeaders(), CASE_LOWER);

        unset($headers['host']);

        ksort($headers);

        $msg = '';
        foreach ($headers as $name => $values) {
            $values = is_array($values) ?
                $values : [$values];

            $msg .= mb_strtolower($name).':'.self::SP.implode(',', $values).self::CRLF;
        }

        return $msg;
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
