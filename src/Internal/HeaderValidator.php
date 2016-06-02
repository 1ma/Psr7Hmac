<?php

namespace UMA\Psr\Http\Message\Internal;

use Psr\Http\Message\MessageInterface;

class HeaderValidator
{
    private $rules = [];

    /**
     * @param string $header
     * @param string $rule
     *
     * @return HeaderValidator
     */
    public function addRule($header, $rule)
    {
        $this->rules[$header] = $rule;

        return $this;
    }

    /**
     * @param MessageInterface $message
     *
     * @return array|bool
     */
    public function conforms(MessageInterface $message)
    {
        $allMatches = [];

        foreach ($this->rules as $header => $rule) {
            if (0 === preg_match($rule, $message->getHeaderLine($header), $matches)) {
                return false;
            }

            $allMatches[$header] = $matches;
        }

        return $allMatches;
    }
}
