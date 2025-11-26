<?php

namespace Mementohub\Data\Exceptions;

use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Helpers\Dump;

class CastingException extends \RuntimeException
{
    public function __construct(
        string $message = '',
        ?DataProperty $property = null,
        mixed $input = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            $this->buildMessage($message, $property, $input),
            $previous?->getCode(),
            $previous
        );
    }

    protected function buildMessage(string $message, ?DataProperty $property, mixed $input): string
    {
        $message = $message;

        if ($property) {
            $message .= "\n".$property;
        }

        $message .= "\n".Dump::var($input);

        return $message;
    }
}
