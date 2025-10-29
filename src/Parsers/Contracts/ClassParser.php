<?php

namespace Mementohub\Data\Parsers\Contracts;

interface ClassParser
{
    public function parse(mixed $payload): mixed;
}
