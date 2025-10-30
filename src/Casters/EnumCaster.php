<?php

namespace Mementohub\Data\Casters;

use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Entities\DataProperty;

class EnumCaster implements Caster
{
    public function __construct(
        protected readonly DataProperty $property,
        protected readonly string $class,
    ) {}

    public function cast(mixed $value, array $context): mixed
    {
        return $this->class::from($value);
    }
}
