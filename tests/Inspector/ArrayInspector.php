<?php

namespace UMA\Tests\Psr7Hmac\Inspector;

use Psr\Http\Message\MessageInterface;
use UMA\Psr7Hmac\Inspector\InspectorInterface;
use UMA\Psr7Hmac\Specification;

class ArrayInspector implements InspectorInterface
{
    /**
     * @var string[]
     */
    private $seenNonces = [];

    /**
     * {@inheritdoc}
     */
    public function vet(MessageInterface $message, $verified)
    {
        if (!$verified) {
            return false;
        }

        $nonce = $message->getHeaderLine(Specification::NONCE_HEADER);

        if (in_array($nonce, $this->seenNonces)) {
            return false;
        }

        $this->seenNonces[] = $nonce;

        return true;
    }
}
