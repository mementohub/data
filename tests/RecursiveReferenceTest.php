<?php

namespace Mementohub\Data\Tests;

use Mementohub\Data\Data;
use PHPUnit\Framework\TestCase;

class RecursiveReferenceTest extends TestCase
{
    public function test_it_parses_recursive_data_with_missing_reference()
    {

        $region = Region578341::from([
            'name' => 'Europe',
            'parent' => null,
        ]);

        $this->assertInstanceOf(Region578341::class, $region);
        $this->assertEquals('Europe', $region->name);
        $this->assertNull($region->parent);
    }

    public function test_it_parses_recursive_data_with_existing_reference()
    {
        $region = Region578341::from([
            'name' => 'Paris',
            'parent' => Region578341::from([
                'name' => 'France',
                'parent' => Region578341::from([
                    'name' => 'Europe',
                    'parent' => null,
                ]),
            ]),
        ]);

        $this->assertInstanceOf(Region578341::class, $region);
        $this->assertEquals('Paris', $region->name);
        $this->assertInstanceOf(Region578341::class, $region->parent);
        $this->assertEquals('France', $region->parent->name);
        $this->assertInstanceOf(Region578341::class, $region->parent->parent);
        $this->assertEquals('Europe', $region->parent->parent->name);
        $this->assertNull($region->parent->parent->parent);
    }

    public function test_it_transforms_recursive_data_with_missing_reference_in_except()
    {
        $region = Region578341::from([
            'name' => 'Rome',
            'parent' => Region578341::from([
                'name' => 'France',
                'parent' => Region578341::from([
                    'name' => 'Europe',
                    'parent' => null,
                ]),
            ]),
        ]);

        $this->assertEquals([
            'name' => 'Rome',
            'parent' => [
                'name' => 'France',
                'parent' => [
                    'name' => 'Europe',
                    'parent' => null,
                ],
            ],
        ], $region->toArray());

    }
}

class Region578341 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?Region578341 $parent,
    ) {}
}
