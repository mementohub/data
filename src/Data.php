<?php

namespace Mementohub\Data;

use Mementohub\Data\Factories\ParserFactory;

abstract class Data
{
    public static function from(array $payload): static
    {

        return ParserFactory::for(static::class)
            ?->handle($payload)
            ?? new static(...$payload);
    }

    public static function normalizers(): array
    {
        return [];
    }
}
