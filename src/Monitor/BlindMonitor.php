<?php

namespace UMA\Psr\Http\Message\Monitor;

use Psr\Http\Message\MessageInterface;

class BlindMonitor implements MonitorInterface
{
    /**
     * {@inheritdoc}
     */
    public function seen(MessageInterface $message)
    {
        return false;
    }
}
