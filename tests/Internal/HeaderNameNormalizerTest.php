<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac;

use PHPUnit\Framework\TestCase;
use UMA\Psr7Hmac\Internal\HeaderNameNormalizer;

final class HeaderNameNormalizerTest extends TestCase
{
    public function testNormalizer(): void
    {
        $nameNormalizer = new HeaderNameNormalizer();

        self::assertSame('foo', $nameNormalizer->normalize('foo'));
        self::assertSame('foo', $nameNormalizer->normalize('Foo'));
        self::assertSame('foo', $nameNormalizer->normalize('HTTP_FOO'));
        self::assertSame('foo-bar', $nameNormalizer->normalize('Foo-Bar'));
        self::assertSame('foo_bar', $nameNormalizer->normalize('Foo_Bar'));
        self::assertSame('foo-bar', $nameNormalizer->normalize('HTTP_FOO_BAR'));
        self::assertSame('content-length', $nameNormalizer->normalize('CONTENT_LENGTH'));
        self::assertSame('content-type', $nameNormalizer->normalize('CONTENT_TYPE'));
    }
}
