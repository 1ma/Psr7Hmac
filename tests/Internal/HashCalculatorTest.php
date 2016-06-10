<?php

namespace UMA\Tests\Psr\Http\Message;

use UMA\Psr\Http\Message\Internal\HashCalculator;

class HashCalculatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider vectorProvider
     *
     * @param string $key
     * @param string $data
     * @param string $digest
     */
    public function testVector($key, $data, $digest)
    {
        if (32 > strlen($digest)) { // Test Case 5 happens to be a beautiful, unique snowflake
            $this->assertStringStartsWith($digest, base64_decode((new HashCalculator())->hmac($data, $key), true));
        } else {
            $this->assertSame($digest, base64_decode((new HashCalculator())->hmac($data, $key), true));
        }
    }

    /**
     * @see https://tools.ietf.org/html/rfc4231#section-4
     */
    public function vectorProvider()
    {
        return [
            'Test Case 1' => [
                hex2bin('0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b'),
                'Hi There',
                hex2bin('b0344c61d8db38535ca8afceaf0bf12b881dc200c9833da726e9376c2e32cff7'),
            ],

            'Test Case 2' => [
                'Jefe',
                'what do ya want for nothing?',
                hex2bin('5bdcc146bf60754e6a042426089575c75a003f089d2739839dec58b964ec3843'),
            ],

            'Test Case 3' => [
                hex2bin('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'),
                hex2bin('dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd'),
                hex2bin('773ea91e36800e46854db8ebd09181a72959098b3ef8c122d9635514ced565fe'),
            ],

            'Test Case 4' => [
                hex2bin('0102030405060708090a0b0c0d0e0f10111213141516171819'),
                hex2bin('cdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcd'),
                hex2bin('82558a389a443c0ea4cc819899f2083a85f0faa3e578f8077a2e3ff46729665b'),
            ],

            'Test Case 5' => [
                hex2bin('0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c'),
                'Test With Truncation',
                hex2bin('a3b6167473100ee06e0c796c2955552b'),
            ],

            'Test Case 6' => [
                hex2bin('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'),
                'Test Using Larger Than Block-Size Key - Hash Key First',
                hex2bin('60e431591ee0b67f0d8a26aacbf5b77f8e0bc6213728c5140546040f0ee37f54'),
            ],

            'Test Case 7' => [
                hex2bin('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'),
                'This is a test using a larger than block-size key and a larger than block-size data. The key needs to be hashed before being used by the HMAC algorithm.',
                hex2bin('9b09ffa71b942fcb27635fbcd5b0e944bfdc63644f0713938a7f51535c3a35e2'),
            ],
        ];
    }
}
