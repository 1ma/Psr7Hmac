<?php

namespace UMA\Tests\Psr7Hmac\Factory;

trait StringifierHelper
{
    /**
     * @param array $headers
     *
     * @return array
     */
    private static function stringify(array $headers)
    {
        foreach ($headers as $name => $value) {
            if (!is_string($value) && !is_array($value)) {
                $headers[$name] = (string) $value;
            }
        }

        return $headers;
    }
}
