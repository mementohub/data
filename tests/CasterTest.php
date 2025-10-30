<?php

namespace Mementohub\Data\Tests;

use DateTimeImmutable;
use Illuminate\Support\Collection;
use Mementohub\Data\Attributes\CastUsing;
use Mementohub\Data\Attributes\CollectionOf;
use Mementohub\Data\Casters\DateTimeCaster;
use Mementohub\Data\Casters\EnumCaster;
use Mementohub\Data\Data;
use Mementohub\Data\Tests\Stubs\Casters\AgeCaster;
use PHPUnit\Framework\TestCase;

class CasterTest extends TestCase
{
    public function test_it_casts()
    {
        $person = Caster9123::from([
            'name' => 'John',
            'email' => 'john@example.com',
            'age' => 30,
        ]);

        $this->assertInstanceOf(Caster9123::class, $person);
        $this->assertEquals('John', $person->name);
        $this->assertEquals('john@example.com', $person->email);
        $this->assertEquals(33, $person->age);
    }

    public function test_it_casts_collection()
    {
        $person = Caster9124::from([
            'name' => 'John',
            'email' => 'john@example.com',
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

        $this->assertInstanceOf(Caster9124::class, $person);
        $this->assertEquals('John', $person->name);
        $this->assertEquals('john@example.com', $person->email);
        $this->assertInstanceOf(Collection::class, $person->children);
        $this->assertCount(2, $person->children);
        $this->assertEquals('Jimmy', $person->children->first()->name);
        $this->assertEquals(5, $person->children->first()->age);
        $this->assertEquals('Johnny', $person->children->last()->name);
        $this->assertEquals(10, $person->children->last()->age);
    }

    public function test_it_casts_enum()
    {
        $person = Person2398::from([
            'name' => 'John',
            'email' => 'john@example.com',
            'gender' => 'male',
        ]);

        $this->assertInstanceOf(Person2398::class, $person);
        $this->assertEquals('John', $person->name);
        $this->assertEquals('john@example.com', $person->email);
        $this->assertInstanceOf(Gender12521::class, $person->gender);
        $this->assertEquals('male', $person->gender->value);
    }

    public function test_it_casts_datetime()
    {
        $book = Book24198::from([
            'name' => 'John',
            'author' => 'John Doe',
            'published' => '2023-01-01',
        ]);

        $this->assertInstanceOf(Book24198::class, $book);
        $this->assertEquals('John', $book->name);
        $this->assertEquals('John Doe', $book->author);
        $this->assertInstanceOf(DateTimeImmutable::class, $book->published);
        $this->assertEquals('2023-01-01', $book->published->format('Y-m-d'));
    }
}

class Caster9123 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $email,
        #[CastUsing(AgeCaster::class)]
        public readonly int $age = 30,
    ) {}
}

class Caster9124 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $email,
        #[CollectionOf(Child981234::class)]
        public readonly Collection $children,
    ) {}
}

class Child981234 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly int $age,
    ) {}
}

class Person2398 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $email,
        #[CastUsing(EnumCaster::class, Gender12521::class)]
        public readonly Gender12521 $gender,
    ) {}
}

enum Gender12521: string
{
    case male = 'male';
    case female = 'female';
}

class Book24198 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $author,
        #[CastUsing(DateTimeCaster::class)]
        public readonly DateTimeImmutable $published,
    ) {}
}
