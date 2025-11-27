<?php

namespace Mementohub\Data\Tests;

use BackedEnum;
use DateTimeImmutable;
use Illuminate\Support\Collection;
use Mementohub\Data\Data;
use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Tests\Stubs\Child;
use Mementohub\Data\Tests\Stubs\Person as PersonAlias;
use PHPUnit\Framework\TestCase;

class InferredCasterTest extends TestCase
{
    public function test_it_detects_enum()
    {
        $class = new DataClass(Person2318::class);
        $property = $class->getProperties()['gender'];

        $this->assertEquals(Gender1652::class, $property->getType()->firstOf(BackedEnum::class));
    }

    public function test_it_casts_iferred_enum()
    {
        $person = Person2318::from([
            'name' => 'John',
            'email' => 'john@example.com',
            'gender' => 'male',
        ]);

        $this->assertInstanceOf(Person2318::class, $person);
        $this->assertEquals('John', $person->name);
        $this->assertEquals('john@example.com', $person->email);
        $this->assertInstanceOf(Gender1652::class, $person->gender);
        $this->assertEquals('male', $person->gender->value);
    }

    public function test_it_detects_collection()
    {
        $class = new DataClass(Album2318::class);
        $property = $class->getProperties()['genres'];

        $this->assertEquals(Genre2318::class, $property->inferArrayTypeFromDocBlock());
    }

    public function test_it_handles_inferred_collection()
    {
        $album = Album2318::from([
            'name' => 'John',
            'author' => 'John Doe',
            'genres' => [
                [
                    'name' => 'genre1',
                    'description' => 'description1',
                ],
                [
                    'name' => 'genre2',
                    'description' => 'description2',
                ],
            ],
        ]);

        $this->assertInstanceOf(Album2318::class, $album);
        $this->assertInstanceOf(Collection::class, $album->genres);
        $this->assertCount(2, $album->genres);
        $this->assertInstanceOf(Genre2318::class, $album->genres->first());
        $this->assertEquals('genre1', $album->genres->first()->name);
        $this->assertEquals('description1', $album->genres->first()->description);
        $this->assertInstanceOf(Genre2318::class, $album->genres->last());
        $this->assertEquals('genre2', $album->genres->last()->name);
        $this->assertEquals('description2', $album->genres->last()->description);
    }

    public function test_it_detects_collection_with_no_type()
    {
        $class = new DataClass(Album79823::class);
        $property = $class->getProperties()['genres'];

        $this->assertNull($property->inferArrayTypeFromDocBlock());
    }

    public function test_it_handles_not_castable_collection()
    {
        $album = Album79823::from([
            'name' => 'John',
            'author' => 'John Doe',
            'genres' => [
                'genre1',
                'genre2',
            ],
        ]);

        $this->assertInstanceOf(Album79823::class, $album);
        $this->assertInstanceOf(Collection::class, $album->genres);
        $this->assertCount(2, $album->genres);
        $this->assertEquals('genre1', $album->genres->first());
        $this->assertEquals('genre2', $album->genres->last());
    }

    public function test_it_detects_used_classes_as_collection_type()
    {
        $class = new DataClass(Father79823::class);
        $property = $class->getProperties()['children'];

        $this->assertEquals(Child::class, $property->inferArrayTypeFromDocBlock());
    }

    public function test_it_detects_aliased_classes_as_collection_type()
    {
        $class = new DataClass(Person81313::class);
        $property = $class->getProperties()['friends'];

        $this->assertEquals(PersonAlias::class, $property->inferArrayTypeFromDocBlock());
    }

    public function test_it_handles_collections_of_enums()
    {
        $album = Album9129342::from([
            'name' => 'John',
            'author' => 'John Doe',
            'genres' => [
                'genre1',
                'genre2',
            ],
        ]);

        $this->assertInstanceOf(Album9129342::class, $album);
        $this->assertInstanceOf(Collection::class, $album->genres);
        $this->assertCount(2, $album->genres);
        $this->assertInstanceOf(Genre9129342::class, $album->genres->first());
        $this->assertEquals('genre1', $album->genres->first()->value);
        $this->assertInstanceOf(Genre9129342::class, $album->genres->last());
        $this->assertEquals('genre2', $album->genres->last()->value);
    }

    public function test_it_handles_array_of_enums()
    {
        $album = Album9129343::from([
            'name' => 'John',
            'author' => 'John Doe',
            'genres' => [
                'genre1',
                'genre2',
            ],
        ]);

        $this->assertInstanceOf(Album9129343::class, $album);
        $this->assertIsArray($album->genres);
        $this->assertCount(2, $album->genres);
        $this->assertInstanceOf(Genre9129342::class, $album->genres[0]);
        $this->assertEquals('genre1', $album->genres[0]->value);
        $this->assertInstanceOf(Genre9129342::class, $album->genres[1]);
        $this->assertEquals('genre2', $album->genres[1]->value);
    }

    public function test_it_handles_datetime()
    {
        $person = Person71243::from([
            'name' => 'John',
            'birthday' => '2023-01-01',
        ]);

        $this->assertInstanceOf(Person71243::class, $person);
        $this->assertEquals('John', $person->name);
        $this->assertEquals('2023-01-01', $person->birthday->format('Y-m-d'));
    }
}

class Person2318 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $email,
        public readonly Gender1652 $gender,
    ) {}
}

enum Gender1652: string
{
    case male = 'male';
    case female = 'female';
}

class Album2318 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $author,
        /** @var Collection<Genre2318> */
        public readonly Collection $genres,
    ) {}
}

class Genre2318 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
    ) {}
}

class Album9129342 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $author,
        /** @var Collection<int, Genre9129342> */
        public readonly Collection $genres,
    ) {}
}

class Album9129343 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $author,
        /** @var Genre9129342[] */
        public readonly array $genres,
    ) {}
}

enum Genre9129342: string
{
    case genre1 = 'genre1';
    case genre2 = 'genre2';
}

class Album79823 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $author,
        public readonly Collection $genres,
    ) {}
}

class Father79823 extends Data
{
    public function __construct(
        public readonly string $name,
        /** @var Collection<int, Child> */
        public readonly Collection $children,
    ) {}
}

class Person81313 extends Data
{
    public function __construct(
        public readonly string $name,
        /** @var Collection<PersonAlias> */
        public readonly Collection $friends,
    ) {}
}

class Person71243 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly DateTimeImmutable $birthday,
    ) {}
}
