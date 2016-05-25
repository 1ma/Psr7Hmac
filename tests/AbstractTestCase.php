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
            ->addFactory($asikaFactory = new AsikaFactory())
            ->addFactory($guzzleFactory = new GuzzleFactory())
            ->addFactory($phyrexiaFactory = new PhyrexiaFactory())
            ->addFactory($ringCentralFactory = new RingCentralFactory())
            ->addFactory($slimFactory = new SlimFactory())
            ->addFactory($wanduFactory = new WanduFactory())
            ->addFactory($zendFactory = new ZendFactory());

        self::$responseProvider = (new ResponseProvider())
            ->addFactory($asikaFactory)
            ->addFactory($guzzleFactory)
            ->addFactory($phyrexiaFactory)
            ->addFactory($ringCentralFactory)
            ->addFactory($slimFactory)
            ->addFactory($wanduFactory)
            ->addFactory($zendFactory);
    }
}
