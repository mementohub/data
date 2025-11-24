<?php

namespace Mementohub\Data\Tests;

use DateTimeImmutable;
use Mementohub\Data\Attributes\DateTimeFormat;
use Mementohub\Data\Data;
use Mementohub\Data\Values\Optional;
use PHPUnit\Framework\TestCase;

class TransformerTest extends TestCase
{
    public function test_it_transforms_simple_data()
    {
        $person = Person98141::from([
            'name' => 'John',
            'email' => 'john@example.com',
            'age' => 30,
        ]);

        $this->assertEquals([
            'name' => 'John',
            'email' => 'john@example.com',
            'age' => 30,
        ], $person->toArray());
    }

    public function test_it_transforms_enum()
    {
        $person = GenderedPerson98141::from([
            'name' => 'John',
            'gender' => 'male',
        ]);

        $this->assertEquals([
            'name' => 'John',
            'gender' => 'male',
        ], $person->toArray());
    }

    public function test_it_transforms_datetime()
    {
        $person = PersonWithBirthday98141::from([
            'name' => 'John',
            'birthday' => '2023-01-01',
        ]);

        $this->assertEquals([
            'name' => 'John',
            'birthday' => '2023-01-01',
        ], $person->toArray());
    }

    public function test_it_transforms_collection()
    {
        $person = PersonWithChildren98141::from([
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
        ], $person->toArray());
    }

    public function test_it_transforms_optional()
    {
        $person = PersonWithOptionalAge98141::from([
            'name' => 'John',
        ]);

        $this->assertEquals([
            'name' => 'John',
        ], $person->toArray());
    }
}

class Person98141 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $email,
        public readonly int $age,
    ) {}
}

class GenderedPerson98141 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly Gender98141 $gender,
    ) {}
}

class PersonWithBirthday98141 extends Data
{
    public function __construct(
        public readonly string $name,
        #[DateTimeFormat(output: 'Y-m-d')]
        public readonly DateTimeImmutable $birthday,
    ) {}
}

class PersonWithChildren98141 extends Data
{
    public function __construct(
        public readonly string $name,
        /** @var Child98141[] */
        public readonly array $children,
    ) {}
}

class Child98141 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly int $age,
    ) {}
}

class PersonWithOptionalAge98141 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly Optional|int $age,
    ) {}
}

enum Gender98141: string
{
    case male = 'male';
    case female = 'female';
}
