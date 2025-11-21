<?php

namespace Mementohub\Data\Parsers;

use Mementohub\Data\Contracts\Parser;
use Mementohub\Data\Entities\DataClass;

class EnumParser implements Parser
{
    protected array $cached = [];

    public function __construct(
        public readonly DataClass $class
    ) {}

    public function handle(mixed $data): mixed
    {
        if (is_string($data) || is_int($data)) {
            return $this->cached[$data] ??= $this->class->name::from($data);
        }

        return $data;
    }
}
