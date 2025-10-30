<?php

namespace Mementohub\Data\Casters;

use Illuminate\Support\Collection;
use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Parsers\Contracts\ClassParser;
use Mementohub\Data\Parsers\Factories\ClassParserFactory;

class CollectionCaster implements Caster
{
    protected readonly ClassParser $parser;

    public function __construct(
        protected readonly DataProperty $property,
        protected readonly string $class
    ) {
        $this->parser = ClassParserFactory::for($class);
    }

    public function cast(mixed $value, array $context): mixed
    {
        $collection = [];
        foreach ($value as $key => $item) {
            $collection[$key] = $this->parser->parse($item);
        }

        return new Collection($collection);
    }
}
