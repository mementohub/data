<?php

namespace Mementohub\Data\Casters;

use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Entities\DataProperty;

class EnumCaster implements Caster
{
    protected array $cached = [];

    public function __construct(
        protected readonly DataProperty $property,
        protected readonly string $class,
    ) {}

    public function handle(mixed $value, array $context): mixed
    {
        if (is_string($value) || is_int($value)) {
            return $this->class::from($value);
        }

        return $value;
    }
}
