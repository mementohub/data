<?php

namespace Mementohub\Data;

use Mementohub\Data\Factories\Parsers;

abstract class Data
{
    public static function from(array $payload): static
    {
        foreach (Parsers::for(static::class) as $parser) {
            $payload = $parser->handle($payload);
        }

        return $payload;
    }

    public static function normalizers(): array
    {
        return [];
    }
}
