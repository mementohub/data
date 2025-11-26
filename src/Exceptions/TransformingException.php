<?php

namespace Mementohub\Data\Exceptions;

use Mementohub\Data\Helpers\Dump;

class TransformingException extends \RuntimeException
{
    public function __construct(
        string $message = '',
        mixed $input = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            $this->buildMessage($message, $input),
            $previous?->getCode(),
            $previous
        );
    }

    protected function buildMessage(string $message, mixed $input): string
    {
        return $message."\n".Dump::var($input);
    }
}
