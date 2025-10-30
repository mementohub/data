<?php

namespace Mementohub\Data\Tests;

use Mementohub\Data\Tests\Stubs\PersonWithAgeNormalizer;
use PHPUnit\Framework\TestCase;

class NormalizerTest extends TestCase
{
    public function test_it_normalizes_data()
    {
        $person = PersonWithAgeNormalizer::from([
            'name' => 'John',
            'email' => 'john@example.com',
            'age' => 30,
        ]);
        $this->assertInstanceOf(PersonWithAgeNormalizer::class, $person);
        $this->assertEquals('John', $person->name);
        $this->assertEquals('john@example.com', $person->email);
        $this->assertEquals(35, $person->age);
    }
}
