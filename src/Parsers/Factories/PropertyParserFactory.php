<?php

namespace Mementohub\Data\Parsers\Factories;

use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Parsers\Contracts\PropertyParser;
use Mementohub\Data\Parsers\Property\DataPropertyParser;
use Mementohub\Data\Parsers\Property\PlainPropertyParser;

class PropertyParserFactory
{
    public function __construct(
        protected readonly DataProperty $property
    ) {}

    public static function for(DataProperty $property): PropertyParser
    {
        return new self($property)->resolve();
    }

    protected function resolve(): PropertyParser
    {
        if ($this->property->getType()->isBuiltin()) {
            return new PlainPropertyParser($this->property);
        }

        return new DataPropertyParser($this->property);
    }
}
