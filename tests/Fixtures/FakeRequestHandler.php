<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Fixtures;

use LogicException;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * A fake RequestHandler for the HmacMiddlewareTest scenarios.
 * When the $boom parameter is true the handle() method will
 * throw an exception if it is executed. If not, an empty HTTP
 * response will be returned with the supplied $statusCode.
 *
 * If $boom is false then $statusCode is mandatory.
 */
class FakeRequestHandler implements RequestHandlerInterface
{
    /**
     * @var bool
     */
    private $boom;

    /**
     * @var int|null
     */
    private $statusCode;

    public function __construct(bool $boom, int $statusCode = null)
    {
        if (!$boom && null === $statusCode) {
            throw new LogicException('$statusCode cannot be left empty when $boom is false');
        }

        $this->boom = $boom;
        $this->statusCode = $statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->boom) {
            throw new LogicException('This handler should not have been reached!');
        }

        return new Response($this->statusCode);
    }
}
