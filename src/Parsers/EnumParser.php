<?php

namespace Mementohub\Data\Parsers;

use Mementohub\Data\Contracts\Parser;
use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Exceptions\ParsingException;

class EnumParser implements Parser
{
    protected array $cached = [];

    public function __construct(
        public readonly DataClass $class
    ) {}

    public function handle(mixed $value): mixed
    {
        if (is_string($value) || is_int($value)) {
            try {
                return $this->cached[$value] ??= $this->class->name::from($value);
            } catch (\Throwable $t) {
                throw new ParsingException('Unable to create '.$this->class->name.' from '.$value, $this->class, $value, $t);
            }
        }

        return $value;
    }
}
