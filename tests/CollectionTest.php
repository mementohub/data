<?php

namespace Mementohub\Data\Tests;

use Generator;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Mementohub\Data\Data;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function test_simple_array()
    {
        $owner = Owner87123::from([
            'pets' => [
                [
                    'type' => 'cat',
                    'name' => 'Fluffy',
                ],
                [
                    'type' => 'dog',
                    'name' => 'Spot',
                ],
            ],
        ]);

        $this->assertInstanceOf(Owner87123::class, $owner);
        $this->assertCount(2, $owner->pets);
        $this->assertInstanceOf(Pet87123::class, $owner->pets[0]);
        $this->assertEquals('cat', $owner->pets[0]->type->value);
        $this->assertEquals('Fluffy', $owner->pets[0]->name);
        $this->assertInstanceOf(Pet87123::class, $owner->pets[1]);
        $this->assertEquals('dog', $owner->pets[1]->type->value);
        $this->assertEquals('Spot', $owner->pets[1]->name);
    }

    public function test_it_handles_collection()
    {
        $owner = Owner87123Collection::from([
            'pets' => [
                [
                    'type' => 'cat',
                    'name' => 'Fluffy',
                ],
                [
                    'type' => 'dog',
                    'name' => 'Spot',
                ],
            ],
        ]);

        $this->assertInstanceOf(Owner87123Collection::class, $owner);
        $this->assertInstanceOf(Collection::class, $owner->pets);
        $this->assertCount(2, $owner->pets);
        $this->assertInstanceOf(Pet87123::class, $owner->pets[0]);
        $this->assertEquals('cat', $owner->pets[0]->type->value);
        $this->assertEquals('Fluffy', $owner->pets[0]->name);
        $this->assertInstanceOf(Pet87123::class, $owner->pets[1]);
        $this->assertEquals('dog', $owner->pets[1]->type->value);
        $this->assertEquals('Spot', $owner->pets[1]->name);
    }

    public function test_it_handles_lazy_collection()
    {
        $owner = Owner87123Lazy::from([
            'pets' => [
                [
                    'type' => 'cat',
                    'name' => 'Fluffy',
                ],
                [
                    'type' => 'dog',
                    'name' => 'Spot',
                ],
            ],
        ]);

        $this->assertInstanceOf(Owner87123Lazy::class, $owner);
        $this->assertInstanceOf(LazyCollection::class, $owner->pets);
        $this->assertCount(2, $owner->pets);
        $this->assertInstanceOf(Pet87123::class, $owner->pets->first());
        $this->assertEquals('cat', $owner->pets->first()->type->value);
        $this->assertEquals('Fluffy', $owner->pets->first()->name);
        $this->assertInstanceOf(Pet87123::class, $owner->pets->last());
        $this->assertEquals('dog', $owner->pets->last()->type->value);
        $this->assertEquals('Spot', $owner->pets->last()->name);
    }

    public function test_it_handles_generator()
    {

        $owner = Owner87123Generator::from([
            'pets' => [
                [
                    'type' => 'cat',
                    'name' => 'Fluffy',
                ],
                [
                    'type' => 'dog',
                    'name' => 'Spot',
                ],
            ],
        ]);

        $this->assertInstanceOf(Owner87123Generator::class, $owner);
        $this->assertInstanceOf(Generator::class, $owner->pets);

        foreach ($owner->pets as $pet) {
            $this->assertInstanceOf(Pet87123::class, $pet);
        }
    }
}

class Owner87123 extends Data
{
    public function __construct(
        /** @var Pet87123[] */
        public readonly array $pets = []
    ) {}
}

class Owner87123Collection extends Data
{
    public function __construct(
        /** @var Collection<int, Pet87123> */
        public readonly Collection $pets
    ) {}
}

class Owner87123Lazy extends Data
{
    public function __construct(
        /** @var LazyCollection<string, Pet87123> */
        public readonly LazyCollection $pets
    ) {}
}

class Owner87123Generator extends Data
{
    public function __construct(
        /** @var Generator<int, Pet87123> */
        public readonly Generator $pets
    ) {}
}

class Pet87123 extends Data
{
    public function __construct(
        public readonly Type87123 $type,
        public readonly string $name,
    ) {}
}

enum Type87123: string
{
    case cat = 'cat';
    case dog = 'dog';
}
