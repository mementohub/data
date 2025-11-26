<?php

namespace Mementohub\Data\Tests;

use Mementohub\Data\Data;
use PHPUnit\Framework\TestCase;

class CloneableTest extends TestCase
{
    public function test_it_can_clone()
    {
        $pet = Pet7242::from([
            'name' => 'Fluffy',
            'type' => 'dog',
        ]);

        $clone = $pet->clone();

        $this->assertNotSame($pet, $clone);
        $this->assertEquals($pet->name, $clone->name);
        $this->assertEquals($pet->type, $clone->type);
    }

    public function test_it_can_clone_with_replacements()
    {
        $pet = Pet7242::from([
            'name' => 'Fluffy',
            'type' => 'dog',
        ]);

        $clone = $pet->clone([
            'name' => 'Fluffy the dog',
        ]);

        $this->assertNotSame($pet, $clone);
        $this->assertEquals('Fluffy the dog', $clone->name);
        $this->assertEquals($pet->type, $clone->type);
    }
}

class Pet7242 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly Type7242 $type,
    ) {}
}

enum Type7242: string
{
    case cat = 'cat';
    case dog = 'dog';
}
