# Psr7Hmac

An HMAC authentication library built on top of the PSR-7 specification.

[![Build Status](https://travis-ci.org/1ma/Psr7Hmac.svg?branch=master)](https://travis-ci.org/1ma/Psr7Hmac) [![Coverage Status](https://coveralls.io/repos/github/1ma/Psr7Hmac/badge.svg?branch=master)](https://coveralls.io/github/1ma/Psr7Hmac?branch=master&bust=1) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/1ma/Psr7Hmac/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/1ma/Psr7Hmac/?branch=master) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/8c7c772a-5819-426d-bef9-eb9f2b4a3102/mini.png)](https://insight.sensiolabs.com/projects/8c7c772a-5819-426d-bef9-eb9f2b4a3102)

## Library API

```php

new Authenticator('secret');

/**
 * @param MessageInterface $message
 *
 * @return MessageInterface
 */
Authenticator::sign(MessageInterface $message);

/**
 * @param MessageInterface $message
 *
 * @return bool
 */
Authenticator::verify(MessageInterface $message);
```


## Demo Script

```php
use UMA\Psr\Http\Message\HMAC\Authenticator;
use UMA\Psr\Http\Message\Serializer\MessageSerializer;
use Zend\Diactoros\Request;

$psr7request = new Request('http://www.example.com/index.html', 'GET');

MessageSerializer::serialize($psr7request);
// GET /index.html HTTP/1.1
// Host: www.example.com

$authenticator = new Authenticator('secret');
$signedRequest = $authenticator->sign($psr7request);

MessageSerializer::serialize($signedRequest);
// GET /index.html HTTP/1.1
// Host: www.example.com
// Authorization: HMAC-SHA256 VxY9dnOd8jjuXuzaC5/Gp9GQ5whB9a3X+BJlgAfD/7g=
// Signed-Headers: Host,Signed-Headers

$authenticator->verify($signedRequest);
// true

$otherAuthenticator = new Authenticator('superSecret');

$otherAuthenticator->verify($signedRequest);
// false
```


## External Resources

* [[PSR-7] HTTP message interfaces](http://www.php-fig.org/psr/psr-7/)
* [[RFC 2104] HMAC: Keyed-Hashing for Message Authentication](https://tools.ietf.org/rfc/rfc2104.txt)
* [[RFC 7230] Hypertext Transfer Protocol (HTTP/1.1): Message Syntax and Routing](https://tools.ietf.org/rfc/rfc7230.txt)
* [[RFC 7235] Hypertext Transfer Protocol (HTTP/1.1): Authentication](https://tools.ietf.org/rfc/rfc7235.txt)
