# Psr7Hmac

An HMAC authentication library built on top of the PSR-7 specification.

[![Build Status](https://travis-ci.org/1ma/Psr7Hmac.svg?branch=master)](https://travis-ci.org/1ma/Psr7Hmac) [![Coverage Status](https://coveralls.io/repos/github/1ma/Psr7Hmac/badge.svg?branch=master)](https://coveralls.io/github/1ma/Psr7Hmac?branch=master&bust=1) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/1ma/Psr7Hmac/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/1ma/Psr7Hmac/?branch=master) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/8c7c772a-5819-426d-bef9-eb9f2b4a3102/mini.png)](https://insight.sensiolabs.com/projects/8c7c772a-5819-426d-bef9-eb9f2b4a3102)


## Library API

```php
/**
 * @param string $secret
 */
Signer::__construct($secret);

/**
 * @param MessageInterface $message
 *
 * @return MessageInterface
 */
Signer::sign(MessageInterface $message);

/**
 * @param MessageInterface $message
 * @param string           $secret
 *
 * @return bool
 */
Verifier::verify(MessageInterface $message, $secret);
```


## Demo Script

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use UMA\Psr\Http\Message\HMAC\Signer;
use UMA\Psr\Http\Message\HMAC\Verifier;
use UMA\Psr\Http\Message\Serializer\MessageSerializer;


$psr7request = new \Zend\Diactoros\Request('http://www.example.com/index.html', 'GET');

var_dump(MessageSerializer::serialize($psr7request));
// GET /index.html HTTP/1.1
// host: www.example.com

$authenticator = new Signer('secret');
$signedRequest = $authenticator->sign($psr7request);

var_dump(MessageSerializer::serialize($signedRequest));
// GET /index.html HTTP/1.1
// host: www.example.com
// authorization: HMAC-SHA256 63IQ8RWDbC9p4ipNrkJz0e0UeGiBrR96zkNdujE5cl8=
// signed-headers: host,signed-headers

$verifier = new Verifier();
var_dump($verifier->verify($signedRequest, 'secret'));
// true

var_dump($verifier->verify($signedRequest, 'another secret'));
// false

// Headers added after calling sign() do not break the verification, as
// they are not included in the signed-headers list.
$signedRequest = $signedRequest->withHeader('User-Agent', 'PHP/5.x');
var_dump($verifier->verify($signedRequest, 'secret'));
// true

// Changes made to any chunk of data that was present at the time of the signature
// are still detected, though.
$signedRequest = $signedRequest->withHeader('Signed-Headers', 'made,up,list');
var_dump($verifier->verify($signedRequest, 'secret'));
// false

```


## External Resources

* [[PSR-7] HTTP message interfaces](http://www.php-fig.org/psr/psr-7/)
* [[RFC 2104] HMAC: Keyed-Hashing for Message Authentication](https://tools.ietf.org/rfc/rfc2104.txt)
* [[RFC 7230] Hypertext Transfer Protocol (HTTP/1.1): Message Syntax and Routing](https://tools.ietf.org/rfc/rfc7230.txt)
* [[RFC 7235] Hypertext Transfer Protocol (HTTP/1.1): Authentication](https://tools.ietf.org/rfc/rfc7235.txt)
