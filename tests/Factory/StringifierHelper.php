<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Factory;

trait StringifierHelper
{
    /**
     * @param string[] $headers
     */
    private static function stringify(array $headers): array
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
