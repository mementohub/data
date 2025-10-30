<?php

namespace Mementohub\Data\Tests;

use Mementohub\Data\Attributes\MapInputName;
use Mementohub\Data\Data;
use PHPUnit\Framework\TestCase;

class InputMapperTest extends TestCase
{
    public function test_it_maps_input()
    {
        $person = InputMaping31421::from([
            'nume' => 'John',
            'email' => 'john@example.com',
            'age' => 30,
        ]);

        $this->assertInstanceOf(InputMaping31421::class, $person);
        $this->assertEquals('John', $person->name);
        $this->assertEquals('john@example.com', $person->email);
        $this->assertEquals(30, $person->age);
    }

    public function test_it_maps_input_with_nested_properties()
    {
        $person = InputMaping31422::from([
            'some' => [
                'deep' => [
                    'nested' => [
                        'property' => 'John',
                    ],
                ],
            ],
            'email' => 'john@example.com',
            'age' => 30,

        ]);

        $this->assertInstanceOf(InputMaping31422::class, $person);
        $this->assertEquals('John', $person->name);
        $this->assertEquals('john@example.com', $person->email);
        $this->assertEquals(30, $person->age);
    }
}

class InputMaping31421 extends Data
{
    public function __construct(
        #[MapInputName('nume')]
        public readonly string $name,
        public readonly ?string $email,
        public readonly int $age = 30,
    ) {}
}

class InputMaping31422 extends Data
{
    public function __construct(
        #[MapInputName('some.deep.nested.property')]
        public readonly string $name,
        public readonly ?string $email,
        public readonly int $age = 30,
    ) {}
}
