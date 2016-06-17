<?php

namespace UMA\Psr7Hmac\Inspector;

use Psr\Http\Message\MessageInterface;

interface InspectorInterface
{
    /**
     * @param MessageInterface $message
     * @param bool             $verified
     *
     * @return bool
     */
    public function vet(MessageInterface $message, $verified);
}
