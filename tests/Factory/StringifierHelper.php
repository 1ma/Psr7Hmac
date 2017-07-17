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
            if (is_array($value)) {
                $headers[$name] = static::stringify($value);
            } elseif (!is_string($value)) {
                $headers[$name] = (string) $value;
            }
        }

        return $headers;
    }
}
