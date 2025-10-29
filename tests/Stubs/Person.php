<?php

namespace Mementohub\Data\Tests\Stubs;

use Mementohub\Data\Data;

class Person extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $email,
        public readonly int $age = 30,
    ) {}
}
