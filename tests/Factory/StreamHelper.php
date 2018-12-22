<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Factory;

trait StreamHelper
{
    /**
     * @return resource
     */
    private static function stream(?string $data)
    {
        if (null === $data) {
            $data = '';
        }

        $stream = fopen('php://memory', 'r+b');

        fwrite($stream, $data);
        rewind($stream);

        return $stream;
    }
}
