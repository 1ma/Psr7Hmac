<?php

namespace UMA\Psr7Hmac\Internal;

class HeaderNameNormalizer
{
    private static $specialSnowflakes = [
        'CONTENT_LENGTH' => 'content-length',
        'CONTENT_TYPE' => 'content-type',
    ];

    /**
     * @param string $name
     *
     * @return string
     */
    public function normalize($name)
    {
        if (array_key_exists($name, self::$specialSnowflakes)) {
            return self::$specialSnowflakes[$name];
        }

        $normalized = mb_strtolower($name);

        if (0 === strpos($normalized, 'http_')) {
            $normalized = str_replace('_', '-', substr($normalized, 5));
        }

        return $normalized;
    }
}
