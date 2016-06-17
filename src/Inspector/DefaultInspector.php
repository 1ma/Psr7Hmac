<?php

namespace UMA\Psr7Hmac\Inspector;

use Psr\Http\Message\MessageInterface;

class DefaultInspector implements InspectorInterface
{
    /**
     * {@inheritdoc}
     */
    public function vet(MessageInterface $message, $verified)
    {
        return true;
    }
}
