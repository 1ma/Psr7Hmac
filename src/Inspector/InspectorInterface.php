<?php

namespace UMA\Psr7Hmac\Inspector;

use Psr\Http\Message\MessageInterface;

/**
 * Inspector classes can hook after the HMAC verification
 * process and examine the whole HTTP message, check the
 * verification outcome or force a failure.
 *
 * Typical use cases for the implementers of this
 * interface include the following:
 *  - Monitoring failed verification attempts
 *  - Tracking duplicate messages by means of a nonce value
 *  - Enforcing additional verification rules upon the HTTP message
 */
interface InspectorInterface
{
    /**
     * Once injected into a Verifier instance, the vet() method
     * will be called once for every call to Verifier::verify(),
     * right after the HMAC verification outcome is determined.
     *
     * The first argument is the MessageInterface object exactly
     * as received by Verifier::verify(), the second is whether
     * it passed the HMAC verification or not.
     *
     * A correct implementation of vet() must return a boolean
     * that signals whether the inspector accepts the given message
     * or not. By means of this mechanism the Inspector can veto messages
     * that passed the HMAC verification. When the $verified argument is
     * false the returned boolean has no effect (i.e. it can only
     * force verification failures, not successes).
     *
     * @param MessageInterface $message
     * @param bool             $verified
     *
     * @return bool
     */
    public function vet(MessageInterface $message, $verified);
}
