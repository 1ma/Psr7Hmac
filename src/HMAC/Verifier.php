<?php

namespace UMA\Psr\Http\Message\HMAC;

use Psr\Http\Message\MessageInterface;
use UMA\Psr\Http\Message\Internal\HashCalculator;
use UMA\Psr\Http\Message\Internal\HeaderValidator;
use UMA\Psr\Http\Message\Monitor\BlindMonitor;
use UMA\Psr\Http\Message\Monitor\MonitorInterface;
use UMA\Psr\Http\Message\Serializer\MessageSerializer;

class Verifier
{
    /**
     * @var HashCalculator
     */
    private $calculator;

    /**
     * @var null|int
     */
    private $maxDelay = null;

    /**
     * @var MonitorInterface
     */
    private $monitor;

    /**
     * @var HeaderValidator
     */
    private $validator;

    public function __construct()
    {
        $this->calculator = new HashCalculator();
        $this->monitor = new BlindMonitor();
        $this->validator = (new HeaderValidator())
            ->addRule(Specification::AUTH_HEADER, Specification::AUTH_REGEXP)
            ->addRule(Specification::SIGN_HEADER, Specification::SIGN_REGEXP);
    }

    /**
     * @param int $maxDelay
     *
     * @return Verifier
     */
    public function setMaximumDelay($maxDelay)
    {
        $this->maxDelay = $maxDelay;

        return $this;
    }

    /**
     * @param MonitorInterface $monitor
     *
     * @return Verifier
     */
    public function setMonitor(MonitorInterface $monitor)
    {
        $this->monitor = $monitor;

        return $this;
    }

    /**
     * @param MessageInterface $message
     * @param string           $secret
     *
     * @return bool Signature verification outcome.
     *
     * @throws \InvalidArgumentException When $message is an implementation of
     *                                   MessageInterface that cannot be
     *                                   serialized and thus neither verified.
     */
    public function verify(MessageInterface $message, $secret)
    {
        if (false === $matches = $this->validator->conforms($message)) {
            return false;
        }

        $clientSideSignature = $matches[Specification::AUTH_HEADER][1];

        $serverSideSignature = $this->calculator
            ->hmac(MessageSerializer::serialize($this->withoutUnsignedHeaders($message)), $secret);

        return
            hash_equals($serverSideSignature, $clientSideSignature) &&
            !$this->delayed($message) &&
            !$this->monitor->seen($message);
    }

    /**
     * @param MessageInterface $message
     *
     * @return MessageInterface
     */
    private function withoutUnsignedHeaders(MessageInterface $message)
    {
        $signedHeaders = array_filter(explode(',', $message->getHeaderLine(Specification::SIGN_HEADER)));

        foreach ($message->getHeaders() as $name => $value) {
            if (!in_array(mb_strtolower($name), $signedHeaders)) {
                $message = $message->withoutHeader($name);
            }
        }

        return $message;
    }

    /**
     * @param MessageInterface $message
     *
     * @return bool
     */
    private function delayed(MessageInterface $message)
    {
        $currentTimestamp = (new \DateTime('now', new \DateTimeZone('GMT')))->getTimestamp();
        $messageTimestamp = (new \DateTime($message->getHeaderLine(Specification::DATE_HEADER)))->getTimestamp();

        return is_int($this->maxDelay) && $currentTimestamp - $messageTimestamp > $this->maxDelay;
    }
}
