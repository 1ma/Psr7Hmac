<?php

namespace UMA\Tests\Psr7Hmac;

use UMA\Psr7Hmac\Internal\HeaderNameNormalizer;

class HeaderNameNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalizer()
    {
        $nameNormalizer = new HeaderNameNormalizer();

        $this->assertSame('foo', $nameNormalizer->normalize('foo'));
        $this->assertSame('foo', $nameNormalizer->normalize('Foo'));
        $this->assertSame('foo', $nameNormalizer->normalize('HTTP_FOO'));
        $this->assertSame('foo-bar', $nameNormalizer->normalize('Foo-Bar'));
        $this->assertSame('foo_bar', $nameNormalizer->normalize('Foo_Bar'));
        $this->assertSame('foo-bar', $nameNormalizer->normalize('HTTP_FOO_BAR'));
    }
}
