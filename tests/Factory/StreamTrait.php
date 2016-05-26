<?php

namespace UMA\Tests\Psr\Http\Message\Factory;

trait StreamTrait
{
    /**
     * @param string $data
     *
     * @return resource
     */
    private static function stream($data)
    {
        $stream = fopen('php://memory', 'r+');

        fwrite($stream, $data);
        rewind($stream);

        return $stream;
    }
}
