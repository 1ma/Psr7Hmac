<?php

namespace UMA\Psr\Http\Message\Serializer;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MessageSerializer
{
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
        if ($message instanceof RequestInterface) {
            $msg = trim($message->getMethod().' '
                .$message->getRequestTarget())
                .' HTTP/'.$message->getProtocolVersion()
                ."\r\nHost: ".$message->getUri()->getHost();
        } elseif ($message instanceof ResponseInterface) {
            $msg = 'HTTP/'.$message->getProtocolVersion().' '
                .$message->getStatusCode().' '
                .$message->getReasonPhrase();
        } else {
            throw new \InvalidArgumentException('Unknown message type');
        }

        $headers = $message->getHeaders();
        unset($headers['Host']);

        foreach ($headers as $name => $values) {
            $msg .= "\r\n{$name}: ".implode(', ', $values);
        }

        return "{$msg}\r\n\r\n".$message->getBody();
    }
}
