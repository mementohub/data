<?php

namespace Mementohub\Data;

use Mementohub\Data\Parsers\Factories\ClassParserFactory;

abstract class Data
{
    public static function from(array $payload): static
    {
        return ClassParserFactory::for(static::class)->parse($payload);
    }

    public static function normalizers(): array
    {
        return [];
    }
}
