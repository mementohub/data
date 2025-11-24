<?php

namespace Mementohub\Data\Traits;

use Mementohub\Data\Factories\ParserFactory;

trait Parsable
{
    public static function from(array $payload): static
    {

        return ParserFactory::for(static::class)
            ?->handle($payload)
            ?? new static(...$payload);
    }
}
