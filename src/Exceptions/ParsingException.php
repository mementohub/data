<?php

namespace Mementohub\Data\Exceptions;

use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Helpers\Dump;

class ParsingException extends \RuntimeException
{
    public function __construct(
        string $message = '',
        ?DataClass $class = null,
        mixed $input = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            $this->buildMessage($message, $class, $input),
            $previous?->getCode(),
            $previous
        );
    }

    protected function buildMessage(string $message, ?DataClass $class, mixed $input): string
    {
        $message = $message;

        if ($class) {
            $message .= "\n".$class->getName();
        }

        $message .= "\n".Dump::var($input);

        return $message;
    }
}
