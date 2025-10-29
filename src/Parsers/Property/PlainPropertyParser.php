<?php

namespace Mementohub\Data\Parsers\Property;

use Mementohub\Data\Parsers\Contracts\PropertyParser;

class PlainPropertyParser implements PropertyParser
{
    public function parse(mixed $value, array $context): mixed
    {
        return $value;
    }
}
