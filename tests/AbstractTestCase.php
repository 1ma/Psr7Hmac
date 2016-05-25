<?php

namespace UMA\Tests\Psr\Http\Message;

use UMA\Tests\Psr\Http\Message\Factory\AsikaFactory;
use UMA\Tests\Psr\Http\Message\Factory\GuzzleFactory;
use UMA\Tests\Psr\Http\Message\Factory\PhyrexiaFactory;
use UMA\Tests\Psr\Http\Message\Factory\RingCentralFactory;
use UMA\Tests\Psr\Http\Message\Factory\SlimFactory;
use UMA\Tests\Psr\Http\Message\Factory\WanduFactory;
use UMA\Tests\Psr\Http\Message\Factory\ZendFactory;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequestProvider
     */
    protected static $requestProvider;

    /**
     * @var ResponseProvider
     */
    protected static $responseProvider;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
    {
        self::$requestProvider = (new RequestProvider())
            ->addFactory(new AsikaFactory())
            ->addFactory(new GuzzleFactory())
            ->addFactory(new PhyrexiaFactory())
            ->addFactory(new RingCentralFactory())
            ->addFactory(new SlimFactory())
            ->addFactory(new WanduFactory())
            ->addFactory(new ZendFactory());

        self::$responseProvider = (new ResponseProvider())
            ->addFactory(new AsikaFactory())
            ->addFactory(new GuzzleFactory())
            ->addFactory(new PhyrexiaFactory())
            ->addFactory(new RingCentralFactory())
            ->addFactory(new SlimFactory())
            ->addFactory(new WanduFactory())
            ->addFactory(new ZendFactory());
    }
}
