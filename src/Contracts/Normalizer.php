<?php

namespace Mementohub\Data\Contracts;

interface Normalizer
{
    public function normalize(mixed $value): ?array;
}
