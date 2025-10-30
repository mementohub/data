<?php

namespace Mementohub\Data\Tests\Stubs\Normalizers;

use Mementohub\Data\Contracts\Normalizer;

class AgeNormalizer implements Normalizer
{
    public function normalize(mixed $value): ?array
    {
        if (! is_array($value)) {
            return null;
        }

        if (array_key_exists('age', $value)) {
            $value['age'] = (int) $value['age'] + 5;
        }

        return $value;
    }
}
