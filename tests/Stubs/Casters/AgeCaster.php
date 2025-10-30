<?php

namespace Mementohub\Data\Tests\Stubs\Casters;

use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Entities\DataProperty;

class AgeCaster implements Caster
{
    public function __construct(
        protected readonly DataProperty $property,
        protected readonly int $increment = 3,
    ) {}

    public function cast(mixed $value, array $context): mixed
    {
        return $value + $this->increment;
    }
}
