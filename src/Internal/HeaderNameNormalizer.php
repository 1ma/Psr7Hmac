<?php

namespace UMA\Psr7Hmac\Internal;


class HeaderNameNormalizer
{
    /**
     * @param string $name
     *
     * @return string
     */
    public function normalize($name)
    {
        $normalized = mb_strtolower($name);

        if (0 === strpos($normalized, 'http_')) {
            $normalized = str_replace('_', '-', substr($normalized, 5));
        }

        return $normalized;
    }
}
