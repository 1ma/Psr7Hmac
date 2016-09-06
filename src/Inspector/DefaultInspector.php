<?php

namespace UMA\Psr7Hmac\Inspector;

use Psr\Http\Message\MessageInterface;

/**
 * The DefaultInspector class is the default implementation
 * of InspectorInterface, the one that will be used when the
 * optional argument of the Verifier constructor is not supplied.
 *
 * It just blindly agrees with its Verifier.
 */
class DefaultInspector implements InspectorInterface
{
    /**
     * {@inheritdoc}
     */
    public function vet(MessageInterface $message, $verified)
    {
        return $verified;
    }
}
