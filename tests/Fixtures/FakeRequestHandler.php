<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Fixtures;

use LogicException;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UMA\Psr7Hmac\Specification;

/**
 * A fake RequestHandler for the HmacMiddlewareTest scenarios.
 * When the $boom parameter is true the handle() method will
 * throw an exception if it is executed.
 *
 * Additionally, when the $statusCode has certain values the
 * handler will also assert that there exists an attribute in
 * the request with a particular value.
 */
class FakeRequestHandler implements RequestHandlerInterface
{
    private const HTTP_STATUS_TO_HMAC_ERROR_MAPPING = [
        202 => null,
        400 => Specification::ERR_NO_KEY,
        401 => Specification::ERR_NO_SECRET,
        403 => Specification::ERR_BROKEN_SIG
    ];

    /**
     * @var Assert
     */
    private $phpunit;

    /**
     * @var bool
     */
    private $boom;

    /**
     * @var int|null
     */
    private $statusCode;

    public function __construct(Assert $phpunit, bool $boom, int $statusCode = null)
    {
        if (!$boom && null === $statusCode) {
            throw new LogicException('$statusCode cannot be left empty when $boom is false');
        }

        $this->phpunit = $phpunit;
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

        $this->phpunit::assertSame(
            self::HTTP_STATUS_TO_HMAC_ERROR_MAPPING[$this->statusCode],
            $request->getAttribute(Specification::HMAC_ERROR, null)
        );

        return new Response($this->statusCode);
    }
}
