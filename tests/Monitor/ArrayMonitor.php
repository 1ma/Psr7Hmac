<?php

namespace UMA\Tests\Psr\Http\Message\Monitor;

use Psr\Http\Message\MessageInterface;
use UMA\Psr\Http\Message\HMAC\Specification;
use UMA\Psr\Http\Message\Monitor\MonitorInterface;

class ArrayMonitor implements MonitorInterface
{
    /**
     * @var string[]
     */
    private $seenNonces = [];

    /**
     * @param MessageInterface $message
     *
     * @return bool
     */
    public function seen(MessageInterface $message)
    {
        $nonce = $message->getHeaderLine(Specification::NONCE_HEADER);

        if (in_array($nonce, $this->seenNonces)) {
            return true;
        }

        $this->seenNonces[] = $nonce;

        return false;
    }
}
