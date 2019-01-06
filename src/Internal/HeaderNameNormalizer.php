<?php

declare(strict_types=1);

namespace UMA\Psr7Hmac\Internal;

final class HeaderNameNormalizer
{
    private static $specialSnowflakes = [
        'CONTENT_LENGTH' => 'content-length',
        'CONTENT_TYPE' => 'content-type',
    ];

    public static function normalize(string $name): string
    {
        if (\array_key_exists($name, self::$specialSnowflakes)) {
            return self::$specialSnowflakes[$name];
        }

        $normalized = \mb_strtolower($name);

        if (0 === \strpos($normalized, 'http_')) {
            $normalized = \str_replace('_', '-', \substr($normalized, 5));
        }

        return $normalized;
    }
}
