<?php

namespace Mementohub\Data\Parsers\Class;

use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Parsers\Contracts\ClassParser;

class PlainClassParser implements ClassParser
{
    public function __construct(
        public readonly DataClass $class
    ) {}

    public function parse(mixed $data): mixed
    {
        if (! is_array($data)) {
            return $data;
        }

        return $this->class->buildFrom($data);
    }
}
