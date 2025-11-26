<?php

namespace Mementohub\Data\Tests;

use DateTimeImmutable;
use Mementohub\Data\Attributes\DateTimeFormat;
use Mementohub\Data\Data;
use Mementohub\Data\Exceptions\CastingException;
use Mementohub\Data\Exceptions\ParsingException;
use PHPUnit\Framework\TestCase;

class ExceptionsTest extends TestCase
{
    public function test_it_throws_exception_on_collection_of_wrong_type()
    {
        try {
            Team67351::from([
                'name' => 'Team',
                'members' => [
                    [
                        'name' => 'John',
                        'gender' => 'male',
                    ],
                    [
                        'name' => 'Jane',
                        'gender' => 'missing',
                    ],
                ],
            ]);
        } catch (\Throwable $t) {
            $p = $t->getPrevious();
            $this->assertInstanceOf(CastingException::class, $p);
            $this->assertStringContainsString('$members', $p->getMessage());
        }
    }

    public function test_it_throws_exception_on_wrong_datetime_input()
    {
        try {
            Child67351::from([
                'birthday' => '2023-01-01zz',
            ]);
        } catch (\Throwable $t) {
            $p = $t->getPrevious();
            $this->assertInstanceOf(CastingException::class, $p);
            $this->assertStringContainsString('Unable to create DateTimeImmutable', $p->getMessage());
            $this->assertStringContainsString('2023-01-01zz', $p->getMessage());
        }
    }

    public function test_it_throws_exception_on_wrong_datetime_format()
    {
        try {
            ChildWithFormat67351::from([
                'birthday' => '2023-01-01 12:00:00',
            ]);
        } catch (\Throwable $t) {
            $p = $t->getPrevious();
            $this->assertInstanceOf(CastingException::class, $p);
            $this->assertStringContainsString('Unable to create DateTimeImmutable from format Y-m-d', $p->getMessage());
        }
    }

    public function test_it_throws_exception_on_wrong_enum_value()
    {
        try {
            Member67351::from([
                'name' => 'John',
                'gender' => 'missing',
            ]);
        } catch (\Throwable $t) {
            $p = $t->getPrevious();
            $this->assertInstanceOf(CastingException::class, $p);
            $this->assertStringContainsString('$gender', $p->getMessage());
        }
    }

    public function test_it_throws_exception_on_missing_required_arguments()
    {
        try {
            Member67351::from([
                'gender' => 'male',
            ]);
        } catch (\Throwable $t) {
            $this->assertInstanceOf(ParsingException::class, $t);
            $this->assertStringContainsString('--- : name', $t->getMessage());
            $this->assertStringContainsString('    : gender', $t->getMessage());
        }
    }
}

class Team67351 extends Data
{
    public function __construct(
        public readonly string $name,
        /** @var Member67351[] */
        public readonly array $members,
    ) {}
}

class Member67351 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly Gender67351 $gender,
    ) {}
}

enum Gender67351: string
{
    case male = 'male';
    case female = 'female';
}

class Child67351 extends Data
{
    public function __construct(
        public readonly DateTimeImmutable $birthday,
    ) {}
}

class ChildWithFormat67351 extends Data
{
    public function __construct(
        #[DateTimeFormat('Y-m-d')]
        public readonly DateTimeImmutable $birthday,
    ) {}
}
