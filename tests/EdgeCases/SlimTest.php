<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\EdgeCases;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request as SlimRequest;
use UMA\Psr7Hmac\Verifier;

final class SlimTest extends TestCase
{
    private const ORIGINAL_SECRET = 'zme+v5kHoCvD7uqF';

    /**
     * The Slim implementation of MessageInterface::getHeaders() returns the header
     * names exactly as they are sent to the constructor. That means that on a FastCGI execution
     * environment they will have the 'HTTP_FOO_BAR' format instead of the expected 'Foo-Bar'.
     */
    public function testFastCGIHeaderNames(): void
    {
        $liveSlimRequest = SlimRequest::createFromEnvironment(Environment::mock([
            'HOSTNAME' => '97a23161396e',
            'SUPERVISOR_ENABLED' => '1',
            'SUPERVISOR_PROCESS_NAME' => 'fpm',
            'SUPERVISOR_GROUP_NAME' => 'fpm',
            'HOME' => '/root',
            'PATH' => '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin',
            'USER' => 'root',
            'HTTP_CONNECTION' => 'close',
            'HTTP_CONTENT_LENGTH' => '0',
            'HTTP_AUTHORIZATION' => 'HMAC-SHA256 NzRwG8TtHLXb2o6koJWumSd7dpTnfbzsPTxmDU9ymP0=',
            'HTTP_SIGNED_HEADERS' => 'api-key,content-length,content-type,host,signed-headers',
            'HTTP_API_KEY' => 'FDzAMHIgEUE224Hw',
            'HTTP_HOST' => 'localhost',
            'HTTP_USER_AGENT' => 'GuzzleHttp/6.2.1 PHP/7.0.13-1+deb.sury.org~trusty+1',
            'HTTPS' => 'off',
            'SCRIPT_FILENAME' => '/var/www/slim-hmac.test/web/app.php',
            'REDIRECT_STATUS' => '200',
            'SERVER_NAME' => 'slim-hmac.test',
            'SERVER_PORT' => '80',
            'SERVER_ADDR' => '172.23.0.3',
            'REMOTE_PORT' => '50496',
            'REMOTE_ADDR' => '172.23.0.1',
            'SERVER_SOFTWARE' => 'nginx/1.11.6',
            'GATEWAY_INTERFACE' => 'CGI/1.1',
            'REQUEST_SCHEME' => 'http',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'DOCUMENT_ROOT' => '/var/www/slim-hmac.test/web',
            'DOCUMENT_URI' => '/app.php',
            'REQUEST_URI' => '/users',
            'SCRIPT_NAME' => '/app.php',
            'CONTENT_LENGTH' => '0',
            'CONTENT_TYPE' => 'osom/cacauets',
            'REQUEST_METHOD' => 'GET',
            'QUERY_STRING' => '',
            'FCGI_ROLE' => 'RESPONDER',
            'PHP_SELF' => '/app.php',
            'REQUEST_TIME_FLOAT' => 1480957028.6005881,
            'REQUEST_TIME' => 1480957028,
        ]));

        $verifier = new Verifier();

        self::assertTrue($verifier->verify($liveSlimRequest, static::ORIGINAL_SECRET));
    }
}
