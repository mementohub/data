<?php

namespace Mementohub\Data\Parsers\Class;

use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Parsers\Contracts\ClassParser;

/**
 * A plain class can only have nullable value defaults
 */
class PlainClassWithDefaultsParser implements ClassParser
{
    protected string $className;

    protected array $nullable = [];

    protected array $known_properties = [];

    public function __construct(
        public readonly DataClass $class
    ) {
        $this->className = $class->getName();
        $this->nullable = $this->class->getNullDefaultableProperties();
        $this->known_properties = $class->getPropertyKeys();
    }

    public function parse(mixed $data): mixed
    {
        if (! is_array($data)) {
            return $data;
        }

        return new $this->className(...array_merge($this->nullable, array_intersect_key($data, $this->known_properties)));
    }
}
