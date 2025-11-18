<?php

namespace Mementohub\Data\Tests;

use Mementohub\Data\Tests\Stubs\Child;
use Mementohub\Data\Tests\Stubs\Person;
use Mementohub\Data\Tests\Stubs\PersonWithChild;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public function test_simple_data_class()
    {
        $person = Person::from([
            'name' => 'John',
            'email' => 'john@example.com',
            'age' => 30,
        ]);

        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals('John', $person->name);
        $this->assertEquals('john@example.com', $person->email);
        $this->assertEquals(30, $person->age);
    }

    public function test_it_handles_default_values()
    {
        $person = Person::from([
            'name' => 'John',
            'email' => 'john@example.com',
        ]);
        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals('John', $person->name);
        $this->assertEquals('john@example.com', $person->email);
        $this->assertEquals(30, $person->age);
    }

    public function test_it_handles_nullable_properties()
    {
        $person = Person::from([
            'name' => 'John',
        ]);
        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals('John', $person->name);
        $this->assertNull($person->email);
        $this->assertEquals(30, $person->age);
    }

    public function test_it_handles_nested_data()
    {
        $person = PersonWithChild::from([
            'name' => 'John',
            'age' => 30,
            'child' => [
                'name' => 'Jimmy',
                'age' => 5,
            ],
        ]);

        $this->assertInstanceOf(PersonWithChild::class, $person);
        $this->assertInstanceOf(Child::class, $person->child);
        $this->assertEquals('Jimmy', $person->child->name);
        $this->assertEquals(5, $person->child->age);
    }
}
