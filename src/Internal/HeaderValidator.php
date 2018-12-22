<?php

declare(strict_types=1);

namespace UMA\Psr7Hmac\Internal;

use Psr\Http\Message\MessageInterface;

final class HeaderValidator
{
    private $rules = [];

    public function addRule(string $header, string $rule): HeaderValidator
    {
        $this->rules[$header] = $rule;

        return $this;
    }

    /**
     * @return array|bool
     */
    public function conforms(MessageInterface $message)
    {
        $matches = [];

        foreach ($this->rules as $header => $rule) {
            if (0 === \preg_match($rule, $message->getHeaderLine($header), $matches[$header])) {
                return false;
            }
        }

        return $matches;
    }
}
