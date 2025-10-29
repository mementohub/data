<?php

namespace Mementohub\Data\Tests\Stubs;

use Mementohub\Data\Data;

class PersonWithChild extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly int $age,
        public readonly Child $child
    ) {}
}
