<?php

namespace Mementohub\Data\Contracts;

interface Caster
{
    public function cast(mixed $value, array $context): mixed;
}
