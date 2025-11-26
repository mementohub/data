<?php

namespace Mementohub\Data\Tests;

use Mementohub\Data\Data;
use Mementohub\Data\Helpers\Dump;
use PHPUnit\Framework\TestCase;

class DumpTest extends TestCase
{
    public function test_it_can_dump_zero_level()
    {
        $dump = Dump::var($this->getPayload(), 0);

        $this->assertEquals(Album234183::class, $dump);
    }

    public function test_it_can_dump_first_level()
    {
        $dump = Dump::var($this->getPayload(), 1);

        $this->assertStringContainsString(Album234183::class, $dump);
        $this->assertStringContainsString('name', $dump);
        $this->assertStringContainsString('artist', $dump);
        $this->assertStringContainsString(Artist234183::class, $dump);
        $this->assertStringNotContainsString('"Artist"', $dump);
        $this->assertStringContainsString('videos', $dump);
        $this->assertStringContainsString('array(2)', $dump);
        $this->assertStringNotContainsString(Video234183::class, $dump);
    }

    public function test_it_can_dump_second_level()
    {
        $dump = Dump::var($this->getPayload(), 2);

        $this->assertStringContainsString('"Artist"', $dump);
        $this->assertStringContainsString(Video234183::class, $dump);
    }

    public function test_it_can_dump_third_level()
    {
        $dump = Dump::var($this->getPayload(), 3);

        $this->assertStringContainsString(Artist234183::class, $dump);
        $this->assertStringContainsString('pop', $dump);
        $this->assertStringNotContainsString('Other', $dump);
    }

    public function test_it_can_dump_fourth_level()
    {
        $dump = Dump::var($this->getPayload(), 4);

        $this->assertStringContainsString('Other', $dump);
    }

    protected function getPayload(): Album234183
    {
        return Album234183::from([
            'name' => 'Album',
            'artist' => [
                'name' => 'Artist',
                'age' => 30,
            ],
            'videos' => [
                [
                    'name' => 'Video',
                    'duration' => 123,
                    'genre' => 'rock',
                    'artist' => [
                        'name' => 'Other Artist',
                        'age' => 30,
                    ],
                ],
                [
                    'name' => 'Video',
                    'duration' => 123,
                    'genre' => 'pop',
                    'artist' => [
                        'name' => 'Other Artist',
                        'age' => 30,
                    ],
                ],
            ],
        ]);
    }
}

class Video234183 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly int $duration,
        public readonly Genre234183 $genre,
        public readonly Artist234183 $artist,
    ) {}
}

class Artist234183 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly int $age
    ) {}
}

class Album234183 extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly Artist234183 $artist,
        /** @var Video234183[] */
        public readonly array $videos
    ) {}
}

enum Genre234183: string
{
    case ROCK = 'rock';
    case POP = 'pop';
}
