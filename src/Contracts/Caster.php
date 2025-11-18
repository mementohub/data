<?php

namespace Mementohub\Data\Contracts;

interface Caster
{
    public function handle(mixed $value, array $context): mixed;
}
