<?php

namespace Mementohub\Data\Parsers\Class;

use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Parsers\Contracts\ClassParser;

/**
 * A plain class can only have nullable value defaults
 */
class PlainClassWithDefaultsParser implements ClassParser
{
    public function __construct(
        public readonly DataClass $class
    ) {}

    public function parse(mixed $data): mixed
    {
        if (! is_array($data)) {
            return $data;
        }

        return $this->class->buildFromWithDefaults($data);
    }
}
