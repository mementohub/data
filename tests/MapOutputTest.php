<?php

namespace Mementohub\Data\Tests;

use Mementohub\Data\Attributes\MapOutputName;
use Mementohub\Data\Data;
use PHPUnit\Framework\TestCase;

class MapOutputTest extends TestCase
{
    public function test_it_maps_output()
    {
        $person = OutputMapPerson58214::from([
            'name' => 'John',
            'email' => 'john@example.com',
            'age' => 30,
        ]);

        $this->assertEquals([
            'full_name' => 'John',
            'contact_email' => 'john@example.com',
            'age' => 30,
        ], $person->toArray());
    }

    public function test_it_maps_nested_output()
    {
        $person = OutputMapParent58214::from([
            'name' => 'John',
            'child' => [
                'name' => 'Jimmy',
                'age' => 5,
            ],
        ]);

        $this->assertEquals([
            'full_name' => 'John',
            'primary_child' => [
                'full_name' => 'Jimmy',
                'age' => 5,
            ],
        ], $person->toArray());
    }

    public function test_it_maps_collection_output()
    {
        $person = OutputMapCollectionParent58214::from([
            'name' => 'John',
            'children' => [
                [
                    'name' => 'Jimmy',
                    'age' => 5,
                ],
                [
                    'name' => 'Johnny',
                    'age' => 10,
                ],
            ],
        ]);

        $this->assertEquals([
            'full_name' => 'John',
            'offspring' => [
                [
                    'full_name' => 'Jimmy',
                    'age' => 5,
                ],
                [
                    'full_name' => 'Johnny',
                    'age' => 10,
                ],
            ],
        ], $person->toArray());
    }

    public function test_it_maps_output_when_using_except()
    {
        $person = OutputMapPerson58214::from([
            'name' => 'John',
            'email' => 'john@example.com',
            'age' => 30,
        ]);

        $this->assertEquals([
            'contact_email' => 'john@example.com',
            'age' => 30,
        ], $person->except('name')->toArray());
    }
}

class OutputMapPerson58214 extends Data
{
    public function __construct(
        #[MapOutputName('full_name')]
        public readonly string $name,
        #[MapOutputName('contact_email')]
        public readonly string $email,
        public readonly int $age,
    ) {}
}

class OutputMapChild58214 extends Data
{
    public function __construct(
        #[MapOutputName('full_name')]
        public readonly string $name,
        public readonly int $age,
    ) {}
}

class OutputMapParent58214 extends Data
{
    public function __construct(
        #[MapOutputName('full_name')]
        public readonly string $name,
        #[MapOutputName('primary_child')]
        public readonly OutputMapChild58214 $child,
    ) {}
}

class OutputMapCollectionParent58214 extends Data
{
    public function __construct(
        #[MapOutputName('full_name')]
        public readonly string $name,
        #[MapOutputName('offspring')]
        /** @var OutputMapChild58214[] */
        public readonly array $children,
    ) {}
}
