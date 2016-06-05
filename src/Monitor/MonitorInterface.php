<?php

namespace UMA\Psr\Http\Message\Monitor;

use Psr\Http\Message\MessageInterface;

interface MonitorInterface
{
    /**
     * @param MessageInterface $message
     *
     * @return bool
     */
    public function seen(MessageInterface $message);
}
