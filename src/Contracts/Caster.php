<?php

namespace Mementohub\Data\Contracts;

interface Caster
{
    public function parse(mixed $value, array $context): mixed;
}
