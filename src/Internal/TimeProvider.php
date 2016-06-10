<?php

namespace UMA\Psr\Http\Message\Internal;

class TimeProvider
{
    /**
     * Returns an RFC 7321 compliant timestamp.
     *
     * @return string
     *
     * @example Sat, 04 Jun 2016 18:28:44 GMT
     *
     * @see https://tools.ietf.org/html/rfc7231#section-7.1.1.1
     */
    public function currentTime()
    {
        return (new \DateTime('now', new \DateTimeZone('GMT')))
            ->format('D, d M Y H:i:s T');
    }
}
