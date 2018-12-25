<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Fixtures;

use Psr\Http\Message\ServerRequestInterface;
use UMA\Psr7Hmac\Psr15\KeyProviderInterface;

/**
 * A KeyProvider that retrieves the key from the given
 * HTTP header name.
 */
final class HeaderKeyProvider implements KeyProviderInterface
{
    /**
     * @var string
     */
    private $headerName;

    public function __construct(string $headerName)
    {
        $this->headerName = $headerName;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeyFrom(ServerRequestInterface $request): ?string
    {
        $value = $request->getHeaderLine($this->headerName);

        if ('' === $value) {
            return null;
        }

        return $value;
    }
}
