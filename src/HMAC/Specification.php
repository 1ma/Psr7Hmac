<?php

namespace UMA\Psr\Http\Message\HMAC;

final class Specification
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
     * An HTTP message with a Signed-Headers header that is not covered
     * by the HMAC signature will neither pass the HMAC verification.
     *
     * The list itself consists of an alphanumerically sorted sequence of header names
     * concatenated by commas. A valid Signed-Headers header must include its own
     * header name, so at the very least it will be the only header in the list.
     *
     * As per RFC 7230 Section 3.2 commas are not legal characters in a header name,
     * hence there cannot be any ambiguity when parsing the header value.
     *
     * @example Signed-Headers: Api-Key,Content-Type,Host,Signed-Headers
     * @example Signed-Headers: Signed-Headers
     */
    const SIGN_HEADER = 'Signed-Headers';
}
