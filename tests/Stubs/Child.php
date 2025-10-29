<?php

namespace Mementohub\Data\Tests\Stubs;

use Mementohub\Data\Data;

class Child extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly int $age
    ) {}
}
