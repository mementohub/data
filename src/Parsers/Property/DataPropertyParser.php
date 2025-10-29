<?php

namespace Mementohub\Data\Parsers\Property;

use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Parsers\Contracts\ClassParser;
use Mementohub\Data\Parsers\Contracts\PropertyParser;
use Mementohub\Data\Parsers\Factories\ClassParserFactory;

class DataPropertyParser implements PropertyParser
{
    protected readonly ClassParser $class_parser;

    public function __construct(
        public readonly DataProperty $property
    ) {
        $this->class_parser = ClassParserFactory::for($property->getType()->getMainType());
    }

    public function parse(mixed $value, array $context): mixed
    {
        return $this->class_parser->parse($value);
    }
}
