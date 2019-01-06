<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac;

use PHPUnit\Framework\TestCase;
use UMA\Psr7Hmac\Internal\HeaderNameNormalizer;

final class HeaderNameNormalizerTest extends TestCase
{
    public function testNormalizer(): void
    {
        self::assertSame('foo', HeaderNameNormalizer::normalize('foo'));
        self::assertSame('foo', HeaderNameNormalizer::normalize('Foo'));
        self::assertSame('foo', HeaderNameNormalizer::normalize('HTTP_FOO'));
        self::assertSame('foo-bar', HeaderNameNormalizer::normalize('Foo-Bar'));
        self::assertSame('foo_bar', HeaderNameNormalizer::normalize('Foo_Bar'));
        self::assertSame('foo-bar', HeaderNameNormalizer::normalize('HTTP_FOO_BAR'));
        self::assertSame('content-length', HeaderNameNormalizer::normalize('CONTENT_LENGTH'));
        self::assertSame('content-type', HeaderNameNormalizer::normalize('CONTENT_TYPE'));
    }
}
