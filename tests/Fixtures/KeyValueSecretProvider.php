<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Fixtures;

use UMA\Psr7Hmac\Psr15\SecretProviderInterface;

/**
 * A SecretProvider that keeps the key => secret relationship as
 * an in-memory array passed at construction time.
 */
final class KeyValueSecretProvider implements SecretProviderInterface
{
    /**
     * @var string[]
     */
    private $secrets;

    public function __construct(array $secrets)
    {
        $this->secrets = $secrets;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecretFor(string $key): ?string
    {
        return $this->secrets[$key] ?? null;
    }
}
