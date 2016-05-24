<?php

namespace UMA\Psr\Http\Message\Security;

final class HMACSpecification
{
    /**
     * Name of the HTTP header that will hold the authentication
     * credentials (i.e. the HMAC signature). Must conform to RFC7235.
     *
     * @see http://tools.ietf.org/html/rfc7235#section-4.2
     */
    const AUTH_HEADER = 'Authorization';

    /**
     * Authentication credentials prefix. Its purpose is telling the
     * message receiver which kind of data the authentication header is holding.
     *
     * @example Authorization: HMAC-SHA256 y0SLRAxCrIrQhPyKh5XJj1t4AjWcMF6r1X7Nsg4kiJY=
     */
    const AUTH_PREFIX = 'HMAC-SHA256';

    /**
     * Hash algorithm used in conjunction with the HMAC function.
     */
    const HASH_ALGORITHM = 'sha256';

    /**
     * Name of the HTTP header that holds the list of signed headers.
     *
     * When verifying the authenticity on an HTTP message, any header
     * not included in that list must be stripped from the message
     * before attempting to serialize it.
     *
     * An HTTP message without the Signed-Headers header cannot
     * pass the HMAC verification.
     *
     * The list itself consists of an alphanumerically sorted sequence of header names
     * concatenated by commas, or "(none)" if no header was present at the time
     * the HMAC signature was performed.
     *
     * As per RFC 7230 Section 3.2 neither the comma nor the parentheses are
     * legal characters in a header name, hence there cannot be any ambiguity
     * when parsing the header value.
     *
     * @example Signed-Headers: Api-Key,Content-Type,Host
     * @example Signed-Headers: Host
     * @example Signed-Headers: (none)
     */
    const SIGN_HEADER = 'Signed-Headers';

    /**
     * @param string $data The string to be signed
     * @param string $key  The secret with which to sign it
     *
     * @return string A base64-encoded SHA256 hash (so it is guaranteed to be 44 bytes long)
     */
    public static function doHMACSignature($data, $key)
    {
        return base64_encode(hash_hmac(self::HASH_ALGORITHM, $data, $key, true));
    }
}
