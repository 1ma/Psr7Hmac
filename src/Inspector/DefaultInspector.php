<?php

declare(strict_types=1);

namespace UMA\Psr7Hmac\Inspector;

use Psr\Http\Message\MessageInterface;

/**
 * The DefaultInspector class is the default implementation
 * of InspectorInterface, the one that will be used when the
 * optional argument of the Verifier constructor is not supplied.
 *
 * It just blindly agrees with its Verifier.
 */
final class DefaultInspector implements InspectorInterface
{
    /**
     * {@inheritdoc}
     */
    public function vet(MessageInterface $message, bool $verified): bool
    {
        return $verified;
    }
}
