<?php

namespace Mementohub\Data\Parsers\Class;

use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Parsers\Contracts\ClassParser;

class EnumClassParser implements ClassParser
{
    protected array $cached = [];

    public function __construct(
        public readonly DataClass $class
    ) {}

    public function parse(mixed $data): mixed
    {
        if (! is_int($data) && ! is_string($data)) {
            return $data;
        }

        return $this->cached[$data] ??= $this->class->name::from($data);
    }
}
