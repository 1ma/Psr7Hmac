<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac;

trait ReflectionUtil
{
    /**
     * @param object $instance
     * @param string $propertyName
     * @param mixed  $misteryMeat
     */
    private function replaceInstanceProperty($instance, string $propertyName, $misteryMeat): void
    {
        $property = (new \ReflectionClass($instance))
            ->getProperty($propertyName);

        $property->setAccessible(true);
        $property->setValue($instance, $misteryMeat);
    }
}
