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

    public function handle(mixed $data): mixed
    {
        if (is_string($data) || is_int($data)) {
            try {
                return $this->cached[$data] ??= $this->class->name::from($data);
            } catch (\Throwable $t) {
                throw new ParsingException('Unable to create '.$this->class->name.' from '.$data, $this->class, $data, $t);
            }
        }

        return $data;
    }
}
