<?php

namespace Mementohub\Data\Casters;

use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Parsers\Contracts\PropertyParser;

class EnumCaster implements PropertyParser
{
    protected array $cached = [];

    public function __construct(
        protected readonly DataProperty $property,
        protected readonly string $class,
    ) {}

    public function parse(mixed $value, array $context): mixed
    {
        if (isset($this->cached[$value])) {
            return $this->cached[$value];
        }

        return $this->cached[$value] = $this->class::from($value);
    }
}
