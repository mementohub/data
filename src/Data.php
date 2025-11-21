<?php

namespace Mementohub\Data;

use Mementohub\Data\Factories\Parsers;

abstract class Data
{
    public static function from(array $payload): static
    {
        // ray(Parsers::for(static::class));

        return Parsers::for(static::class)?->handle($payload)
            ?? new static(...$payload);
        // if (is_null($parser = Parsers::for(static::class))) {
        //     return new static(...$payload);
        // }

        // return $parser->handle($payload);
    }

    public static function normalizers(): array
    {
        return [];
    }
}
