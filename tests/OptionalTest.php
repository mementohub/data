<?php

namespace Mementohub\Data\Tests;

use Mementohub\Data\Data;
use Mementohub\Data\Values\Optional;
use PHPUnit\Framework\TestCase;

class OptionalTest extends TestCase
{
    public function test_instantiation()
    {
        $person = Person984321::from([
            'name' => 'John',
        ]);

        $this->assertInstanceOf(Person984321::class, $person);
        $this->assertEquals('John', $person->name);
        $this->assertInstanceOf(Optional::class, $person->age);
    }
}

class Person984321 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly Optional|int $age,
    ) {}
}
