<?php

namespace Mementohub\Data\Casters;

use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Contracts\Parser;
use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Factories\Parsers;
use Traversable;

class CollectionCaster implements Caster
{
    protected readonly ?Parser $parser;

    protected readonly ?string $type;

    public function __construct(
        protected readonly DataProperty $property,
        protected readonly ?string $class
    ) {
        if ($class) {
            $this->parser = Parsers::for($class);
        } else {
            $this->parser = null;
        }

        $this->type = $this->property->getType()->firstOf(Traversable::class);
    }

    public function handle(mixed $value, array $context): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (is_null($this->parser)) {
            if (is_null($this->type)) {
                return $value;
            }

            return new $this->type($value);
        }

        $collection = [];
        foreach ($value as $key => $item) {
            $collection[$key] = $this->parser->handle($item, $context);
        }

        if (is_null($this->type)) {
            return $collection;
        }

        return new $this->type($collection);
    }
}
