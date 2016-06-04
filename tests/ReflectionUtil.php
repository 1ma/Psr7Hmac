<?php

namespace UMA\Tests\Psr\Http\Message;

trait ReflectionUtil
{
    /**
     * @param object $instance
     * @param string $propertyName
     * @param mixed  $misteryMeat
     */
    private function replaceInstanceProperty($instance, $propertyName, $misteryMeat)
    {
        $property = (new \ReflectionClass($instance))
            ->getProperty($propertyName);

        $property->setAccessible(true);
        $property->setValue($instance, $misteryMeat);
    }
}
