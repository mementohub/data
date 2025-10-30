<?php

namespace Mementohub\Data\Tests\Stubs;

use Mementohub\Data\Data;
use Mementohub\Data\Tests\Stubs\Normalizers\AgeNormalizer;

class PersonWithAgeNormalizer extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $email,
        public readonly int $age = 30,
    ) {}

    public static function normalizers(): array
    {
        return [
            new AgeNormalizer,
        ];
    }
}
