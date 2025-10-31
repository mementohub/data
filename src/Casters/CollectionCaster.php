<?php

namespace Mementohub\Data\Casters;

use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Parsers\Contracts\ClassParser;
use Mementohub\Data\Parsers\Factories\ClassParserFactory;
use Traversable;

class CollectionCaster implements Caster
{
    protected readonly ClassParser $parser;

    protected readonly ?string $type;

    public function __construct(
        protected readonly DataProperty $property,
        protected readonly ?string $class
    ) {
        if ($class) {
            $this->parser = ClassParserFactory::for($class);
        }

        $this->type = $this->property->getType()->firstOf(Traversable::class);
    }

    public function cast(mixed $value, array $context): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (! isset($this->parser)) {
            if (is_null($this->type)) {
                return $value;
            }

            return new $this->type($value);
        }

        $collection = [];
        foreach ($value as $key => $item) {
            $collection[$key] = $this->parser->parse($item);
        }

        if (is_null($this->type)) {
            return $collection;
        }

        return new $this->type($collection);
    }
}
