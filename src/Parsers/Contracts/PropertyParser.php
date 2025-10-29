<?php

namespace Mementohub\Data\Parsers\Contracts;

interface PropertyParser
{
    public function parse(mixed $value, array $context): mixed;
}
