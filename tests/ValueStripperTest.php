<?php

namespace Mementohub\Data\Tests;

use Mementohub\Data\Attributes\StripValues;
use Mementohub\Data\Data;
use PHPUnit\Framework\TestCase;

class ValueStripperTest extends TestCase
{
    public function test_it_strips_input()
    {
        $person = Sample81723::from([
            'name' => 'John',
            'email' => [],
            'age' => 30,
        ]);

        $this->assertInstanceOf(Sample81723::class, $person);
        $this->assertEquals('John', $person->name);
        $this->assertEquals('fake@example.com', $person->email);
        $this->assertEquals(30, $person->age);
    }
}

class Sample81723 extends Data
{
    public function __construct(
        public readonly string $name,
        #[StripValues([], null)]
        public readonly ?string $email = 'fake@example.com',
        public readonly int $age = 30,
    ) {}
}
