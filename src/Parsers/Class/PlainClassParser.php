<?php

namespace Mementohub\Data\Parsers\Class;

use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Parsers\Contracts\ClassParser;

class PlainClassParser implements ClassParser
{
    protected array $known_properties = [];

    protected string $className;

    public function __construct(
        public readonly DataClass $class
    ) {
        $this->className = $class->getName();
        $this->known_properties = $class->getPropertyKeys();
    }

    public function parse(mixed $data): mixed
    {
        if (! is_array($data)) {
            return $data;
        }

        return new $this->className(...array_intersect_key($data, $this->known_properties));
    }
}
