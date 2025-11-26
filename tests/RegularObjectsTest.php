<?php

namespace Mementohub\Data\Tests;

use Mementohub\Data\Data;
use Mementohub\Data\Factories\ParserFactory;
use Mementohub\Data\Factories\TransformerFactory;
use PHPUnit\Framework\TestCase;

class RegularObjectsTest extends TestCase
{
    public function test_it_can_parse_regular_objects()
    {
        $location = ParserFactory::for(Location87134::class)?->handle([
            'name' => 'Home',
            'address' => [
                'street' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10001',
            ],
        ]);

        $this->assertInstanceOf(Location87134::class, $location);
        $this->assertEquals('Home', $location->name);
        $this->assertInstanceOf(Address87134::class, $location->address);
        $this->assertEquals('123 Main St', $location->address->street);
        $this->assertEquals('New York', $location->address->city);
        $this->assertEquals('NY', $location->address->state);
        $this->assertEquals('10001', $location->address->zip);
    }

    public function test_it_can_parse_data_objects_with_regular_properties()
    {
        $location = ParserFactory::for(LocationData87134::class)?->handle([
            'name' => 'Home',
            'address' => [
                'street' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10001',
            ],
        ]);

        $this->assertInstanceOf(LocationData87134::class, $location);
        $this->assertEquals('Home', $location->name);
        $this->assertInstanceOf(Address87134::class, $location->address);
        $this->assertEquals('123 Main St', $location->address->street);
        $this->assertEquals('New York', $location->address->city);
        $this->assertEquals('NY', $location->address->state);
        $this->assertEquals('10001', $location->address->zip);
    }

    public function test_it_can_parse_regular_objects_with_data_properties()
    {
        $location = ParserFactory::for(LocationWithData87134::class)?->handle([
            'name' => 'Home',
            'address' => [
                'street' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10001',
            ],
        ]);

        $this->assertInstanceOf(LocationWithData87134::class, $location);
        $this->assertEquals('Home', $location->name);
        $this->assertInstanceOf(AddressData87134::class, $location->address);
        $this->assertEquals('123 Main St', $location->address->street);
        $this->assertEquals('New York', $location->address->city);
        $this->assertEquals('NY', $location->address->state);
        $this->assertEquals('10001', $location->address->zip);
    }

    public function test_it_can_transform_regular_objects()
    {
        $location = ParserFactory::for(Location87134::class)?->handle([
            'name' => 'Home',
            'address' => [
                'street' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10001',
            ],
        ]);

        $transformed = TransformerFactory::for(Location87134::class)?->handle($location);

        $this->assertEquals([
            'name' => 'Home',
            'address' => [
                'street' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10001',
            ],
        ], $transformed);
    }

    public function test_it_can_transform_regular_objects_with_data_properties()
    {
        $location = ParserFactory::for(LocationWithData87134::class)?->handle([
            'name' => 'Home',
            'address' => [
                'street' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10001',
            ],
        ]);

        $transformed = TransformerFactory::for(LocationWithData87134::class)?->handle($location);

        $this->assertEquals([
            'name' => 'Home',
            'address' => [
                'street' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10001',
            ],
        ], $transformed);
    }

    public function test_it_can_transform_data_objects_with_regular_properties()
    {
        $location = ParserFactory::for(LocationData87134::class)?->handle([
            'name' => 'Home',
            'address' => [
                'street' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10001',
            ],
        ]);

        $transformed = TransformerFactory::for(LocationData87134::class)?->handle($location);

        $this->assertEquals([
            'name' => 'Home',
            'address' => [
                'street' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10001',
            ],
        ], $transformed);
    }
}

class Location87134
{
    public function __construct(
        public readonly string $name,
        public readonly Address87134 $address,
    ) {}
}

class Address87134
{
    public function __construct(
        public readonly string $street,
        public readonly string $city,
        public readonly string $state,
        public readonly string $zip,
    ) {}
}

class LocationData87134 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly Address87134 $address,
    ) {}
}

class LocationWithData87134
{
    public function __construct(
        public readonly string $name,
        public readonly AddressData87134 $address,
    ) {}
}

class AddressData87134 extends Data
{
    public function __construct(
        public readonly string $street,
        public readonly string $city,
        public readonly string $state,
        public readonly string $zip,
    ) {}
}
